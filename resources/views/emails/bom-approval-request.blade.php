@extends('emails.layout')

@section('title', 'BOM Approval Required')

@section('content')
    <h2 class="email-title">BOM Approval Request</h2>
    
    <div class="email-body">
        <p>Hello {{ $approver->name }},</p>
        
        <p>A new Bill of Materials (BOM) requires your approval.</p>
        
        <div class="alert alert-info">
            <p><strong>BOM Details:</strong></p>
            <ul>
                <li><strong>BOM Number:</strong> {{ $bom->bom_number }}</li>
                <li><strong>Name:</strong> {{ $bom->name }}</li>
                <li><strong>Version:</strong> {{ $bom->version }}</li>
                <li