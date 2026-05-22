@extends('layouts.member.master')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-sm-6">
                <h3>Refill History</h3>
            </div>
            <div class="col-12 col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('member.index') }}"><i data-feather="home"></i></a></li>
                    <li class="breadcrumb-item">History</li>
                    <li class="breadcrumb-item active">Refill History</li>
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
                    <h5>Your Refill Requests</h5>
                    <span>Track and check the status of your order refill requests.</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>Refill ID</th>
                                    <th>Date</th>
                                    <th>Order ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#RF-108</td>
                                    <td>2023-10-15 15:45</td>
                                    <td>#2940</td>
                                    <td>YouTube Subscribers [High Quality]</td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>#RF-102</td>
                                    <td>2023-10-10 11:20</td>
                                    <td>#2891</td>
                                    <td>Instagram Followers [Real & Active]</td>
                                    <td><span class="badge badge-success">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
