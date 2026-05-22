@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>Frequently Asked Questions</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">F.A.Q</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Questions & Answers</h5>
                    <span>Find answers to all your common queries about ordering, payment, and refill.</span>
                </div>
                <div class="card-body">
                    <div class="default-according" id="accordion">
                        <div class="card mb-2">
                            <div class="card-header bg-light" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn btn-link text-dark" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        What is an SMM Panel?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-bs-parent="#accordion">
                                <div class="card-body text-muted">
                                    An SMM (Social Media Marketing) panel is an online store that offers various social media marketing services such as followers, likes, views, subscribers, and comments to boost your online presence.
                                </div>
                            </div>
                        </div>

                        <div class="card mb-2">
                            <div class="card-header bg-light" id="headingTwo">
                                <h5 class="mb-0">
                                    <button class="btn btn-link text-dark collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        How long does it take for an order to start?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-bs-parent="#accordion">
                                <div class="card-body text-muted">
                                    Most orders start within 10-60 minutes after placement. In some cases, it can take up to 24 hours depending on the service volume and updates.
                                </div>
                            </div>
                        </div>

                        <div class="card mb-2">
                            <div class="card-header bg-light" id="headingThree">
                                <h5 class="mb-0">
                                    <button class="btn btn-link text-dark collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        What is the Refill Guarantee?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-bs-parent="#accordion">
                                <div class="card-body text-muted">
                                    If you experience a drop in followers/likes within the warranty period (e.g., 30 days), we will top it up for free when you request a refill from your history tab.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
