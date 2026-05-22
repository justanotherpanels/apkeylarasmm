@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>API Documentation</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">API Documentation</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-sm-12 col-md-3 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>API Sections</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="api-tabs" role="tablist">
                        <a class="list-group-item list-group-item-action active" id="overview-tab" data-bs-toggle="list" href="#overview" role="tab">
                            <i class="fa fa-info-circle me-2"></i> Overview
                        </a>
                        <a class="list-group-item list-group-item-action" id="services-tab" data-bs-toggle="list" href="#services" role="tab">
                            <i class="fa fa-list me-2"></i> Services List
                        </a>
                        <a class="list-group-item list-group-item-action" id="balance-tab" data-bs-toggle="list" href="#balance" role="tab">
                            <i class="fa fa-money me-2"></i> Check Balance
                        </a>
                        <a class="list-group-item list-group-item-action" id="order-tab" data-bs-toggle="list" href="#order" role="tab">
                            <i class="fa fa-shopping-cart me-2"></i> Place Order
                        </a>
                        <a class="list-group-item list-group-item-action" id="status-tab" data-bs-toggle="list" href="#status" role="tab">
                            <i class="fa fa-refresh me-2"></i> Order Status
                        </a>
                        <a class="list-group-item list-group-item-action" id="create-deposit-tab" data-bs-toggle="list" href="#create-deposit" role="tab">
                            <i class="fa fa-plus-circle me-2"></i> Request Deposit
                        </a>
                        <a class="list-group-item list-group-item-action" id="deposit-tab" data-bs-toggle="list" href="#deposit" role="tab">
                            <i class="fa fa-credit-card me-2"></i> Deposit Status
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="col-sm-12 col-md-9">
            <div class="tab-content" id="api-tabContent">
                
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    @if(session('success'))
                        <div class="alert alert-success mt-2">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>General API Overview</h5>
                            <span>Integrate our SMM services into your custom applications or panels.</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>API Endpoint</h6>
                                <div class="bg-light p-3 rounded border">
                                    <code class="text-primary font-weight-bold" style="font-size: 1.1rem;">POST {{ url('/api/v2') }}</code>
                                </div>
                                <small class="text-muted">All API requests must use the HTTP POST method.</small>
                            </div>

                            <div class="mb-4">
                                <h6>Authentication</h6>
                                <p class="text-muted">You must authenticate your API requests by supplying your account API Key in the payload. Keep your API key private and secure.</p>
                                
                                <div class="col-md-8 px-0">
                                    <label class="form-label" for="apiKeyInput">Your API Key</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" value="{{ auth()->user()->api_key }}" id="apiKeyInput" placeholder="No API Key generated yet" readonly>
                                        <button class="btn btn-outline-primary" type="button" onclick="toggleApiKey(event)"><i class="fa fa-eye"></i> Show</button>
                                        @if(auth()->user()->api_key)
                                            <button class="btn btn-outline-info" type="button" onclick="copyApiKey(event)"><i class="fa fa-copy"></i> Copy</button>
                                        @endif
                                    </div>
                                    <form action="{{ route('member.pages.api.generate') }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-key me-1"></i> {{ auth()->user()->api_key ? 'Regenerate API Key' : 'Generate API Key' }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div>
                                <h6>Format & Content-Type</h6>
                                <p class="text-muted">Accepts parameters in standard HTTP POST payload (<code>application/x-www-form-urlencoded</code>) or raw JSON (<code>application/json</code>). The API response is always returned in JSON format.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services List Tab -->
                <div class="tab-pane fade" id="services" role="tabpanel">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Fetch Services List</h5>
                            <span>Retrieve all SMM services available for ordering.</span>
                        </div>
                        <div class="card-body">
                            <h6>Request Parameters</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td>Your API authentication key.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>action</code></td>
                                            <td>string</td>
                                            <td>Set to <code>services</code></td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Example JSON Request</h6>
                            <div class="bg-light p-3 rounded mb-4 border">
<pre class="mb-0"><code>{
  "key": "{{ auth()->user()->api_key ?? 'your_api_key' }}",
  "action": "services"
}</code></pre>
                            </div>

                            <h6>Example JSON Response</h6>
                            <div class="bg-light p-3 rounded border">
<pre class="mb-0"><code>[
  {
    "service": 101,
    "name": "Instagram Followers [High Quality]",
    "type": "Default",
    "rate": "1.25",
    "min": "50",
    "max": "10000",
    "category": "Instagram Followers",
    "refill": true,
    "desc": "Real and high quality Instagram Followers with 30 Days Refill Guarantee."
  },
  {
    "service": 102,
    "name": "YouTube Views [Non-Drop]",
    "type": "Default",
    "rate": "3.50",
    "min": "100",
    "max": "50000",
    "category": "YouTube Views",
    "refill": false,
    "desc": "High quality YouTube Views, instant start time."
  }
]</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check Balance Tab -->
                <div class="tab-pane fade" id="balance" role="tabpanel">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Check User Balance</h5>
                            <span>Verify your remaining balance and account currency.</span>
                        </div>
                        <div class="card-body">
                            <h6>Request Parameters</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td>Your API authentication key.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>action</code></td>
                                            <td>string</td>
                                            <td>Set to <code>balance</code></td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Example JSON Request</h6>
                            <div class="bg-light p-3 rounded mb-4 border">
<pre class="mb-0"><code>{
  "key": "{{ auth()->user()->api_key ?? 'your_api_key' }}",
  "action": "balance"
}</code></pre>
                            </div>

                            <h6>Example JSON Response</h6>
                            <div class="bg-light p-3 rounded border">
<pre class="mb-0"><code>{
  "status": "success",
  "balance": "84.25",
  "currency": "USD"
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Place Order Tab -->
                <div class="tab-pane fade" id="order" role="tabpanel">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Place New Order</h5>
                            <span>Submit a new order request to the platform.</span>
                        </div>
                        <div class="card-body">
                            <h6>Request Parameters</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td>Your API authentication key.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>action</code></td>
                                            <td>string</td>
                                            <td>Set to <code>add</code></td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>service</code></td>
                                            <td>integer</td>
                                            <td>The target SMM service ID.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>link</code></td>
                                            <td>string</td>
                                            <td>Target link URL (e.g. video URL or account page).</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>quantity</code></td>
                                            <td>integer</td>
                                            <td>Number of units to order.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Example JSON Request</h6>
                            <div class="bg-light p-3 rounded mb-4 border">
<pre class="mb-0"><code>{
  "key": "{{ auth()->user()->api_key ?? 'your_api_key' }}",
  "action": "add",
  "service": 101,
  "link": "https://instagram.com/p/ExamplePost",
  "quantity": 1000
}</code></pre>
                            </div>

                            <h6>Example JSON Response</h6>
                            <div class="bg-light p-3 rounded border">
<pre class="mb-0"><code>{
  "status": "success",
  "order": 94812
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Tab -->
                <div class="tab-pane fade" id="status" role="tabpanel">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Check Order Status</h5>
                            <span>Verify progress details, start counts, and remaining counts.</span>
                        </div>
                        <div class="card-body">
                            <h6>Request Parameters</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td>Your API authentication key.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>action</code></td>
                                            <td>string</td>
                                            <td>Set to <code>status</code></td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>order</code></td>
                                            <td>integer</td>
                                            <td>The target SMM Order ID.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Example JSON Request</h6>
                            <div class="bg-light p-3 rounded mb-4 border">
<pre class="mb-0"><code>{
  "key": "{{ auth()->user()->api_key ?? 'your_api_key' }}",
  "action": "status",
  "order": 94812
}</code></pre>
                            </div>

                            <h6>Example JSON Response</h6>
                            <div class="bg-light p-3 rounded border">
<pre class="mb-0"><code>{
  "status": "Completed",
  "charge": "1.25",
  "start_count": "5420",
  "remains": "0",
  "currency": "USD"
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Deposit Tab -->
                <div class="tab-pane fade" id="create-deposit" role="tabpanel">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Request / Create Deposit</h5>
                            <span>Programmatically initiate a PayPal or Cryptomus deposit. Returns a checkout URL.</span>
                        </div>
                        <div class="card-body">
                            <h6>Request Parameters</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td>Your API authentication key.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>action</code></td>
                                            <td>string</td>
                                            <td>Set to <code>create_deposit</code></td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>amount</code></td>
                                            <td>numeric</td>
                                            <td>Deposit amount in USD (minimum: 1.00)</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>payment_method</code></td>
                                            <td>string</td>
                                            <td>Allowed values: <code>paypal</code> or <code>cryptomus</code></td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Example JSON Request</h6>
                            <div class="bg-light p-3 rounded mb-4 border">
<pre class="mb-0"><code>{
  "key": "{{ auth()->user()->api_key ?? 'your_api_key' }}",
  "action": "create_deposit",
  "amount": 20.00,
  "payment_method": "cryptomus"
}</code></pre>
                            </div>

                            <h6>Example JSON Response</h6>
                            <div class="bg-light p-3 rounded border">
<pre class="mb-0"><code>{
  "status": "success",
  "invoice": "DEP-FIHXQWNWPY",
  "checkout_url": "https://pay.cryptomus.com/pay/uuid-xxxx-xxxx"
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deposit Status Tab -->
                <div class="tab-pane fade" id="deposit" role="tabpanel">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Fetch Deposit Status</h5>
                            <span>Retrieve payment receipt metadata and verification status.</span>
                        </div>
                        <div class="card-body">
                            <h6>Request Parameters</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td>Your API authentication key.</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>action</code></td>
                                            <td>string</td>
                                            <td>Set to <code>deposit_status</code></td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td><code>invoice</code></td>
                                            <td>string</td>
                                            <td>The deposit invoice code (e.g. <code>DEP-FIHXQWNWPY</code>).</td>
                                            <td><span class="badge badge-success">Yes</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Example JSON Request</h6>
                            <div class="bg-light p-3 rounded mb-4 border">
<pre class="mb-0"><code>{
  "key": "{{ auth()->user()->api_key ?? 'your_api_key' }}",
  "action": "deposit_status",
  "invoice": "DEP-FIHXQWNWPY"
}</code></pre>
                            </div>

                            <h6>Example JSON Response</h6>
                            <div class="bg-light p-3 rounded border">
<pre class="mb-0"><code>{
  "status": "Success",
  "amount": "20.00",
  "payment_method": "Cryptomus",
  "created_at": "2026-05-19 14:15:30",
  "currency": "USD"
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Container-fluid ends-->

<script>
function toggleApiKey(event) {
    var x = document.getElementById("apiKeyInput");
    var btn = event.currentTarget;
    if (x.type === "password") {
        x.type = "text";
        btn.innerHTML = '<i class="fa fa-eye-slash"></i> Hide';
    } else {
        x.type = "password";
        btn.innerHTML = '<i class="fa fa-eye"></i> Show';
    }
}

function copyApiKey(event) {
    var copyText = document.getElementById("apiKeyInput");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    navigator.clipboard.writeText(copyText.value);
    
    var copyBtn = event.currentTarget;
    var originalHTML = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="fa fa-check"></i> Copied!';
    copyBtn.className = 'btn btn-success';
    setTimeout(function() {
        copyBtn.innerHTML = originalHTML;
        copyBtn.className = 'btn btn-outline-info';
    }, 2000);
}
</script>
@endsection
