<?php

namespace App\Services;

use App\Models\Bom;
use App\Models\Item;
use App\Models\BomVersion;
use App\Models\ItemVersion;
use Illuminate\Support\Facades\Auth;

class VersionService
{
    public function createVersion($model, array $data, $changesDescription = null)
    {
        if ($model instanceof Bom) {
            return BomVersion::create([
                'bom_id' => $model->id,
                'version' => $model->version,
                'data' => json_encode($data),
                'changes_description' => $changesDescription,
                'created_by' => Auth::id()
            ]);
        } elseif ($model instanceof Item) {
            return ItemVersion::create([
                'item_id' => $model->id,
                'version' => $model->version,
                'data' => json_encode($data),
                'changes_description' => $changesDescription,
                'created_by' => Auth::id()
            ]);
        }
        
        throw new \InvalidArgumentException('Unsupported model type');
    }
    
    public function createNewVersion($model, array $newData, $changesDescription = null)
    {
        // Mark old version as not current
        $model->update(['is_current' => false]);
        
        // Create new version of the model
        $newVersionNumber = $model->version + 1;
        
        if ($model instanceof Bom) {
            $newModel = Bom::create(array_merge($newData, [
                'bom_number' => $model->bom_number,
                'version' => $newVersionNumber,
                'is_current' => true,
                'created_by' => Auth::id(),
                'status' => 'draft'
            ]));
        } elseif ($model instanceof Item) {
            $newModel = Item::create(array_merge($newData, [
                'item_code' => $model->item_code,
                'version' => $newVersionNumber,
                'is_current' => true,
                'created_by' => Auth::id()
            ]));
        } else {
            throw new \InvalidArgumentException('Unsupported model type');
        }
        
        // Create version record
        $this->createVersion($newModel, $newData, $changesDescription);
        
        return $newModel;
    }
    
    public function getVersionData($model, $version)
    {
        if ($model instanceof Bom) {
            $versionRecord = BomVersion::where('bom_id', $model->id)
                ->where('version', $version)
                ->first();
        } elseif ($model instanceof Item) {
            $versionRecord = ItemVersion::where('item_id', $model->id)
                ->where('version', $version)
                ->first();
        } else {
            throw new \InvalidArgumentException('Unsupported model type');
        }
        
        return $versionRecord ? json_decode($versionRecord->data, true) : null;
    }
}