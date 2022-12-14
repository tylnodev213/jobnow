@extends('web.layouts.master')
@section('title', 'App - Top Page')

@section('style-libraries')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
@stop

@section('content')
    <section>
        <div class="container">
            <div class="row mt-lg-5 my-lg-3 mt-3 my-0 justify-content-center text-center">
                <div class="text-center col-7 f-s-70">
                    Get The <span class="text-purple">Right Job</span> You Deserve
                </div>
            </div>
            <div class="row my-2 my-md-3 my-lg-5 f-s-18 text-gray justify-content-center">
                <div class="col-lg-4 col-10 my-lg-0 my-2 position-relative  px-0">
                    <input class="w-100 ps-5 input-search0 " placeholder=" Search Title or Keyword" type="text"
                           style="border-radius: 20px 0 0 20px;height: 60px; border: none;border-right: 2px solid #878787 ;box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;">
                    <i class="fa fa-search position-absolute f-s-30" style="top:26%; left:3%"></i>
                </div>
                <div class="col-lg-6 col-10 position-relative  px-0 mb-lg-0 mb-5">
                    <input type="text" placeholder="Search Location" class="w-100 ps-5 input-search"
                           style="border-radius: 0 20px 20px 0px; height: 60px; border: none;box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;">
                    <i class="fa fa-location-dot position-absolute f-s-30" style="top: 26%; left: 3%;"></i>
                    <button class="text-white bg-purble position-absolute input-search-btn py-2"
                            style="width: 119px; height: 60px;  border-radius: 20px; border: none;">search</button>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="row">
                <div class="col text-black text-center f-s-50">
                    Featured Job Circulars
                </div>
            </div>
            <div class="row row-cols-4">
                <div class="col my-4 ">
                    <div class="card " style="border: none; border-radius:15px ;  box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;">
                        <div class="row mt-3 mx-0 align-items-center">
                            <div class="col-3">
                                <img  src="assets/img/logo-company.png" class="card-img-top w-100" alt="...">
                            </div>
                            <div class="col d-flex flex-column f-s-18 text-start">
                                Microsoft
                                <span class="f-s-12 ">Freelancer</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Senior UI Designer</h5>
                            <p class="f-s-12">Fulltime</p>
                            <p class="card-text text-justify text-space">This is a wider card with supporting text below as a natural lead-in to
                                additional content. This content is a little bit longer.</p>
                            <div class="row">
                                <span class="f-s-12 col"><span class="f-s-18" >$2500</span>/month</span>
                                <a href="#" class="f-s-18 text-decoration-none  col text-purple" >Apply now</a>
                            </div>
                        </div>
                        <div class="card-footer " style=" border-top:none">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>
                <div class="col my-4 ">
                    <div class="card " style="border: none; box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to
                                additional content. This content is a little bit longer.</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>
                <div class="col my-4 ">
                    <div class="card " style="border: none; box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to
                                additional content. This content is a little bit longer.</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>
                <div class="col my-4 ">
                    <div class="card " style="border: none; box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to
                                additional content. This content is a little bit longer.</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>

                <div class="col my-4 ">
                    <div class="card " style="border: none; box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to
                                additional content. This content is a little bit longer.</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </section>
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.js"></script>
    {{--jquery.autocomplete.js--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.4.10/jquery.autocomplete.min.js"></script>
    {{--quick defined--}}
@stop
