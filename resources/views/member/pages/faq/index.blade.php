@extends('layouts.member.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Frequently Asked Questions</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('member.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active">F.A.Q</li>
    </ol>

    <div class="row">
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-question-circle me-1"></i> Questions & Answers
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Find answers to all your common queries about ordering, payment, and refill.</p>
                    
                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    What is an SMM Panel?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    An SMM (Social Media Marketing) panel is an online store that offers various social media marketing services such as followers, likes, views, subscribers, and comments to boost your online presence.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How long does it take for an order to start?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Most orders start within 10-60 minutes after placement. In some cases, it can take up to 24 hours depending on the service volume and updates.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    What is the Refill Guarantee?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
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
