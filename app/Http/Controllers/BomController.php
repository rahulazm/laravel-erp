<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\SalesOrder;
use App\Models\Assembly;
use App\Models\Item;
use App\Http\Requests\StoreBomRequest;
use App\Http\Requests\UpdateBomRequest;
use App\Http\Requests\ApproveBomRequest;
use App\Services\VersionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BomController extends Controller
{
    protected $versionService;
    
    public function __construct(VersionService $versionService)
    {
        $this->versionService = $versionService;
        $this->middleware('can:approve,bom')->only(['approve', 'reject']);
    }
    
    public function index(Request $request)
    {
        $query = Bom::with(['createdBy', 'assembly', 'salesOrder'])
            ->where('is_current', true);
            
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bom_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $boms = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('boms.index', compact('boms'));
    }
    
    public function create()
    {
        $salesOrders = SalesOrder::active()->get();
        $assemblies = Assembly::all();
        $items = Item::active()->get();
        
        return view('boms.create', compact('salesOrders', 'assemblies', 'items'));
    }
    
    public function store(StoreBomRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['bom_number'] = $this->generateBomNumber();
        $data['version'] = 1;
        $data['is_current'] = true;
        
        // Create BOM
        $bom = Bom::create($data);
        
        // Create initial version
        $this->versionService->createVersion($bom, $data);
        
        // If approval is required, set status to pending
        if ($request->has('submit_for_approval')) {
            $bom->update(['status' => 'pending_approval']);
            
            // Create approval task for admin
            $this->createApprovalTask($bom);
        }
        
        return redirect()->route('boms.show', $bom)
            ->with('success', 'BOM created successfully.');
    }
    
    public function show(Bom $bom)
    {
        $bom->load(['createdBy', 'approvedBy', 'assembly', 'salesOrder', 'versions']);
        $checkout = $bom->currentCheckout;
        
        return view('boms.show', compact('bom', 'checkout'));
    }
    
    public function edit(Bom $bom)
    {
        // Check if BOM is checked out by current user
        $checkout = $bom->currentCheckout;
        if ($checkout && $checkout->user_id !== Auth::id()) {
            return redirect()->route('boms.show', $bom)
                ->with('error', 'This BOM is currently checked out by another user.');
        }
        
        $salesOrders = SalesOrder::active()->get();
        $assemblies = Assembly::all();
        
        return view('boms.edit', compact('bom', 'salesOrders', 'assemblies'));
    }
    
    public function update(UpdateBomRequest $request, Bom $bom)
    {
        // Check if BOM is checked out by current user
        $checkout = $bom->currentCheckout;
        if (!$checkout || $checkout->user_id !== Auth::id()) {
            return redirect()->route('boms.show', $bom)
                ->with('error', 'You must check out the BOM before editing.');
        }
        
        $data = $request->validated();
        
        // Create new version
        $newVersion = $this->versionService->createNewVersion($bom, $data, $request->changes_description);
        
        // Check in the BOM
        $checkout->checkin();
        
        // If submitted for approval
        if ($request->has('submit_for_approval')) {
            $newVersion->update(['status' => 'pending_approval']);
            $this->createApprovalTask($newVersion);
        }
        
        return redirect()->route('boms.show', $newVersion)
            ->with('success', 'BOM updated successfully.');
    }
    
    public function checkout(Request $request, Bom $bom)
    {
        // Check if already checked out
        if ($bom->currentCheckout) {
            return redirect()->route('boms.show', $bom)
                ->with('error', 'BOM is already checked out.');
        }
        
        // Create checkout record
        $bom->checkouts()->create([
            'user_id' => Auth::id(),
            'checked_out_at' => now(),
            'purpose' => $request->purpose
        ]);
        
        return redirect()->route('boms.edit', $bom)
            ->with('success', 'BOM checked out successfully.');
    }
    
    public function checkin(Request $request, Bom $bom)
    {
        $checkout = $bom->currentCheckout;
        
        if (!$checkout || $checkout->user_id !== Auth::id()) {
            return redirect()->route('boms.show', $bom)
                ->with('error', 'You cannot check in this BOM.');
        }
        
        $checkout->checkin();
        
        return redirect()->route('boms.show', $bom)
            ->with('success', 'BOM checked in successfully.');
    }
    
    public function approve(ApproveBomRequest $request, Bom $bom)
    {
        // Two-level approval workflow
        if (Auth::user()->role === 'company_admin') {
            $bom->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);
            
            // Mark approval task as completed
            $this->completeApprovalTask($bom);
            
            return redirect()->route('boms.index')
                ->with('success', 'BOM approved successfully.');
        }
        
        return redirect()->route('boms.show', $bom)
            ->with('error', 'You are not authorized to approve BOMs.');
    }
    
    public function reject(Request $request, Bom $bom)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        if (Auth::user()->role === 'company_admin') {
            $bom->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);
            
            // Add rejection note
            $bom->notes()->create([
                'content' => $request->rejection_reason,
                'type' => 'rejection',
                'created_by' => Auth::id()
            ]);
            
            // Mark approval task as completed
            $this->completeApprovalTask($bom);
            
            return redirect()->route('boms.index')
                ->with('success', 'BOM rejected successfully.');
        }
        
        return redirect()->route('boms.show', $bom)
            ->with('error', 'You are not authorized to reject BOMs.');
    }
    
    public function versions(Bom $bom)
    {
        $versions = $bom->versions()->orderBy('version', 'desc')->get();
        
        return view('boms.versions', compact('bom', 'versions'));
    }
    
    public function restoreVersion(Bom $bom, $version)
    {
        $oldVersion = $bom->versions()->where('version', $version)->firstOrFail();
        
        // Create new version from old version data
        $newVersion = $this->versionService->createNewVersion(
            $bom,
            $oldVersion->data,
            "Restored from version {$version}"
        );
        
        return redirect()->route('boms.show', $newVersion)
            ->with('success', "BOM restored from version {$version} successfully.");
    }
    
    private function generateBomNumber()
    {
        $prefix = 'BOM';
        $year = date('Y');
        $month = date('m');
        
        $lastBom = Bom::where('bom_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('bom_number', 'desc')
            ->first();
        
        if ($lastBom) {
            $lastNumber = intval(substr($lastBom->bom_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }
    
    private function createApprovalTask($bom)
    {
        // Create task for company admin approval
        Task::create([
            'title' => "Approve BOM: {$bom->bom_number}",
            'description' => "Review and approve BOM {$bom->name}",
            'type' => 'approval',
            'priority' => 'high',
            'assigned_to' => User::where('role', 'company_admin')->first()->id,
            'due_date' => now()->addDays(2),
            'related_type' => 'bom',
            'related_id' => $bom->id,
            'created_by' => Auth::id()
        ]);
        
        // Create notification
        Notification::create([
            'user_id' => User::where('role', 'company_admin')->first()->id,
            'title' => 'BOM Pending Approval',
            'message' => "BOM {$bom->bom_number} is waiting for your approval.",
            'type' => 'approval',
            'related_type' => 'bom',
            'related_id' => $bom->id
        ]);
    }
    
    private function completeApprovalTask($bom)
    {
        Task::where('related_type', 'bom')
            ->where('related_id', $bom->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
    }
}