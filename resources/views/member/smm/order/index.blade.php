@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">New Order</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item active">New Order</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- New Order Form Card -->
        <div class="col-lg-8 mb-4">
            <form action="{{ route('member.smm.order.store') }}" method="POST" id="orderForm">
                @csrf
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i> Place a New Order
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">Select category, service, and enter details to place your SMM order.</p>
                        
                        <!-- Category Select -->
                        <div class="mb-3">
                            <label class="form-label" for="categorySelect">Category</label>
                            <select class="form-select" id="categorySelect" name="id_category_smm" required>
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('id_category_smm', $selectedService ? $selectedService->id_category_smm : '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service Select -->
                        <div class="mb-3">
                            <label class="form-label" for="serviceSelect">Service</label>
                            <select class="form-select" id="serviceSelect" name="id_service_smm" required>
                                <option value="">-- Choose Service --</option>
                            </select>
                        </div>

                        <!-- Service Description -->
                        <div class="mb-3">
                            <label class="form-label" for="serviceDescription">Description</label>
                            <textarea class="form-control" id="serviceDescription" rows="4" readonly placeholder="Service description will be displayed here..."></textarea>
                        </div>

                        <!-- Target input (Dynamic label: Link / Username) -->
                        <div class="mb-3" id="targetGroup">
                            <label class="form-label" id="targetLabel" for="targetInput">Link</label>
                            <input class="form-control" id="targetInput" name="target" type="text" value="{{ old('target') }}" placeholder="https://example.com/..." required>
                        </div>

                        <!-- Quantity Field (Default, Package, Poll) -->
                        <div class="mb-3 d-none" id="quantityGroup">
                            <label class="form-label" for="quantityInput">Quantity</label>
                            <input class="form-control" id="quantityInput" name="amount" type="number" value="{{ old('amount') }}" placeholder="Min - Max">
                        </div>

                        <!-- Comments Field (Custom Comments) -->
                        <div class="mb-3 d-none" id="commentsGroup">
                            <label class="form-label" for="commentsInput">Comments (One per line)</label>
                            <textarea class="form-control" id="commentsInput" name="comments" rows="5" placeholder="Enter comments, one per line...">{{ old('comments') }}</textarea>
                        </div>

                        <!-- Answer Number Field (Poll) -->
                        <div class="mb-3 d-none" id="answerNumberGroup">
                            <label class="form-label" for="answerNumberInput">Answer Number</label>
                            <input class="form-control" id="answerNumberInput" name="answer_number" type="text" value="{{ old('answer_number') }}" placeholder="e.g. 1">
                        </div>

                        <!-- Subscriptions Fields Group -->
                        <div id="subscriptionsGroup" class="d-none">
                            <div class="mb-3">
                                <label class="form-label" for="minInput">Min Quantity per Post</label>
                                <input class="form-control" id="minInput" name="min" type="number" value="{{ old('min') }}" placeholder="e.g. 100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="maxInput">Max Quantity per Post</label>
                                <input class="form-control" id="maxInput" name="max" type="number" value="{{ old('max') }}" placeholder="e.g. 500">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="postsInput">Posts Count</label>
                                <input class="form-control" id="postsInput" name="posts" type="number" value="{{ old('posts') }}" placeholder="e.g. 10">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="delaySelect">Delay</label>
                                <select class="form-select" id="delaySelect" name="delay">
                                    <option value="0" {{ old('delay') == '0' ? 'selected' : '' }}>No Delay</option>
                                    <option value="5" {{ old('delay') == '5' ? 'selected' : '' }}>5 minutes</option>
                                    <option value="10" {{ old('delay') == '10' ? 'selected' : '' }}>10 minutes</option>
                                    <option value="15" {{ old('delay') == '15' ? 'selected' : '' }}>15 minutes</option>
                                    <option value="30" {{ old('delay') == '30' ? 'selected' : '' }}>30 minutes</option>
                                    <option value="60" {{ old('delay') == '60' ? 'selected' : '' }}>60 minutes</option>
                                    <option value="90" {{ old('delay') == '90' ? 'selected' : '' }}>90 minutes</option>
                                </select>
                            </div>
                        </div>

                        <!-- Estimated Price -->
                        <div class="mb-3">
                            <label class="form-label" for="priceInput">Estimated Price</label>
                            <input class="form-control" id="priceInput" type="text" disabled placeholder="$0.00">
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Submit Order</button>
                        <a href="{{ route('member.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4 mb-4">
            <!-- Balance Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-wallet me-1"></i> User Balance
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        <div class="mb-3"><i class="fas fa-wallet fa-3x"></i></div>
                        <h3 class="fw-bold">${{ number_format(auth()->user()->balance, 2) }}</h3>
                        <p class="text-muted small">Your Account Balance</p>
                        <a href="{{ route('member.payment.add') }}" class="btn btn-primary btn-sm mt-2">Deposit Now</a>
                    </div>
                </div>
            </div>
            
            <!-- Rules Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i> Order Rules & Info
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small mb-0">
                        <li class="list-group-item px-0">1. Make sure your account / link is <strong>Public</strong>.</li>
                        <li class="list-group-item px-0">2. Do not place multiple orders for the same link simultaneously.</li>
                        <li class="list-group-item px-0">3. Average start time is 0-24 hours.</li>
                    </ul>
                </div>
            </div>

            <!-- API Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-code me-1"></i> API Integration
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="copyCurlCode()"><i class="fas fa-copy me-1"></i>Copy</button>
                </div>
                <div class="card-body">
                    <p class="text-muted small">You can place orders programmatically using the following PHP cURL snippet:</p>
                    <div class="position-relative">
                        <pre class="bg-light p-2 rounded mb-0" style="overflow-x: auto; max-height: 250px;"><code class="language-php" id="curlCodeSnippet" style="font-size: 11px; white-space: pre-wrap; word-wrap: break-word;"></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function copyCurlCode() {
    const code = document.getElementById('curlCodeSnippet').innerText;
    navigator.clipboard.writeText(code).then(() => {
        alert('cURL snippet copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    const serviceSelect = document.getElementById('serviceSelect');
    const serviceDescription = document.getElementById('serviceDescription');
    const targetLabel = document.getElementById('targetLabel');
    const targetInput = document.getElementById('targetInput');
    const quantityGroup = document.getElementById('quantityGroup');
    const quantityInput = document.getElementById('quantityInput');
    const commentsGroup = document.getElementById('commentsGroup');
    const commentsInput = document.getElementById('commentsInput');
    const answerNumberGroup = document.getElementById('answerNumberGroup');
    const answerNumberInput = document.getElementById('answerNumberInput');
    const subscriptionsGroup = document.getElementById('subscriptionsGroup');
    const minInput = document.getElementById('minInput');
    const maxInput = document.getElementById('maxInput');
    const postsInput = document.getElementById('postsInput');
    const delaySelect = document.getElementById('delaySelect');
    const priceInput = document.getElementById('priceInput');

    const curlCodeSnippet = document.getElementById('curlCodeSnippet');
    const userApiKey = "{{ auth()->user()->api_key ?: 'YOUR_API_KEY' }}";
    const apiEndpoint = "{{ route('api-frontend.order.store') }}";

    // Store fetched services locally
    let loadedServices = [];
    let currentService = null;

    function updateCurlSnippet(serviceId = 'SERVICE_ID') {
        if (!curlCodeSnippet) return;
        curlCodeSnippet.textContent = `$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => '${apiEndpoint}',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
    'action' => 'add',
    'key' => '${userApiKey}',
    'service' => '${serviceId}',
    'link' => 'https://example.com/link',
    'quantity' => '100'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;`;
    }

    updateCurlSnippet();

    // Helper to calculate price
    function calculatePrice() {
        if (!currentService) {
            priceInput.value = '$0.00';
            return;
        }

        const priceSale = parseFloat(currentService.price_sale);
        const type = currentService.type;
        let price = 0;

        if (type === 'Default' || type === 'Package' || type === 'Poll') {
            const qty = parseInt(quantityInput.value) || 0;
            price = (qty * priceSale) / 1000;
        } else if (type === 'Custom Comments') {
            const comments = commentsInput.value.trim();
            if (comments === '') {
                price = 0;
            } else {
                const lines = comments.split('\n').filter(line => line.trim() !== '');
                price = (lines.length * priceSale) / 1000;
            }
        } else if (type === 'Subscriptions') {
            const posts = parseInt(postsInput.value) || 0;
            const max = parseInt(maxInput.value) || 0;
            price = (posts * max * priceSale) / 1000;
        }

        priceInput.value = '$' + price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 });
    }

    // Bind calculatePrice to all relevant inputs
    quantityInput.addEventListener('input', calculatePrice);
    commentsInput.addEventListener('input', calculatePrice);
    maxInput.addEventListener('input', calculatePrice);
    postsInput.addEventListener('input', calculatePrice);

    // Function to hide all dynamic groups and remove required attributes
    function hideAllDynamicFields() {
        quantityGroup.classList.add('d-none');
        commentsGroup.classList.add('d-none');
        answerNumberGroup.classList.add('d-none');
        subscriptionsGroup.classList.add('d-none');

        quantityInput.removeAttribute('required');
        commentsInput.removeAttribute('required');
        answerNumberInput.removeAttribute('required');
        minInput.removeAttribute('required');
        maxInput.removeAttribute('required');
        postsInput.removeAttribute('required');
    }

    // Load services when category changes
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        serviceSelect.innerHTML = '<option value="">-- Choose Service --</option>';
        serviceDescription.value = '';
        currentService = null;
        loadedServices = [];
        calculatePrice();
        hideAllDynamicFields();

        if (!categoryId) return;

        updateCurlSnippet();

        const getServicesUrl = "{{ route('member.smm.get_services', '') }}/" + categoryId;

        fetch(getServicesUrl)
            .then(res => res.json())
            .then(services => {
                loadedServices = services;
                services.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.name_service} - $${parseFloat(service.price_sale).toFixed(4)}/1000`;
                    serviceSelect.appendChild(option);
                });

                // Check if there is an old value or pre-selected service to select
                const oldServiceId = "{{ old('id_service_smm', $selectedService ? $selectedService->id : '') }}";
                if (oldServiceId) {
                    serviceSelect.value = oldServiceId;
                    serviceSelect.dispatchEvent(new Event('change'));
                }
            });
    });

    // Handle service changes
    serviceSelect.addEventListener('change', function() {
        const serviceId = this.value;
        serviceDescription.value = '';
        currentService = null;
        calculatePrice();
        hideAllDynamicFields();

        if (!serviceId) {
            updateCurlSnippet();
            return;
        }

        updateCurlSnippet(serviceId);

        const service = loadedServices.find(s => s.id == serviceId);
        if (!service) return;

        currentService = service;
        serviceDescription.value = service.desc || 'No description available for this service.';

        const type = service.type;
        if (type === 'Default' || type === 'Package') {
            targetLabel.textContent = 'Link';
            targetInput.placeholder = 'https://example.com/username';
            quantityGroup.classList.remove('d-none');
            quantityInput.setAttribute('required', 'required');
            quantityInput.placeholder = `Min: ${service.min_order} - Max: ${service.max_order}`;
        } else if (type === 'Custom Comments') {
            targetLabel.textContent = 'Link';
            targetInput.placeholder = 'https://example.com/username';
            commentsGroup.classList.remove('d-none');
            commentsInput.setAttribute('required', 'required');
            commentsInput.placeholder = `Enter comments, one per line (Min: ${service.min_order} - Max: ${service.max_order})`;
        } else if (type === 'Poll') {
            targetLabel.textContent = 'Link';
            targetInput.placeholder = 'https://example.com/username';
            quantityGroup.classList.remove('d-none');
            quantityInput.setAttribute('required', 'required');
            quantityInput.placeholder = `Min: ${service.min_order} - Max: ${service.max_order}`;
            answerNumberGroup.classList.remove('d-none');
            answerNumberInput.setAttribute('required', 'required');
        } else if (type === 'Subscriptions') {
            targetLabel.textContent = 'Username';
            targetInput.placeholder = 'Enter target username';
            subscriptionsGroup.classList.remove('d-none');
            minInput.setAttribute('required', 'required');
            maxInput.setAttribute('required', 'required');
            postsInput.setAttribute('required', 'required');
        }

        calculatePrice();
    });

    // Handle old category value trigger on page load
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
