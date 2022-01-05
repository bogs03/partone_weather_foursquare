<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>City Guide</title>
    <style>
			body{
				background-image: url('https://source.unsplash.com/1600x900/?{{$bgimg}}');
			}
			#weather {
				background: #000000d9;
				color: white;
			}
            .form-inline .form-group {
                margin-right:0px;
            }
		</style>
</head>
<body>
  
    <div class="container">
        <br>
        <div class="row">
            <div class="col-sm-12">
                <div class="d-flex justify-content-center">
                    <h5 class="text-white">City Guide</h5>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">
                <div class="d-flex justify-content-center">
                    <form class="form-inline" action="/cityguide" method="POST">
                        @csrf
                        <div class="form-check-inline  ml-0 pl-0">
                            <label for="store"></label>
                            <input class="form-control" type="text" name="store" id="store" placeholder="I'm looking for...">
                        </div>                        
                        <div class="form-check-inline ml-0 pl-0">
                            <label for="city"></label>
                            <input class="form-control" type="text" name="city" id="city" placeholder="Enter city name" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary mb-2">Search Now</button>
                           @if($error==404 || $error==400 || isset($_GET['error']))
                           <div class="alert alert-danger" role="alert">
                            City not found!
                            </div>
                           @endif
                    </form> 
                </div>   
            </div>   
        </div>
        <br>
        @if(!empty($from) && ($error!=404 && $error!=400))

        <div class="row justify-content-center">
            <div class="col-sm-7">
                <div class="card" id="weather">
                    <h5 class="card-header">Result</h5>
                    <div class="card-body">
                        <ul class="list-group">
                            @if(!empty($postfoursquare['response']['minivenues']))
                                @foreach($postfoursquare['response'] as $venues)
                                    @foreach($venues as $venue)
                                        <?php
                                            $address="";
                                            $city ="";
                                            $state="";
                                            if(!empty($venue['location']['address'])){
                                                $address = $venue['location']['address'];
                                            }
                                            if(!empty($venue['location']['city'])){
                                                $city = $venue['location']['city'];
                                            }
                                            if(!empty($venue['location']['state'])){
                                                $state = $venue['location']['state'];
                                            }                                        
                                        ?>
                                        <li class="list-group-item">
                                            <h6 class="card-title text-primary">{{ucwords($venue['name'])}}</h6>
                                            <span class="align-middle">Address: {{$address. ', '.$city . ', ' . $state }}</span>
                                        </li> 
                                    @endforeach
                                @endforeach
                            @else
                            <li class="list-group-item">
                                <span class="text-center text-danger">No records found!</span>
                            </li> 
                            @endif                   
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="card" id="weather">
                    <div class="card-header">
                        <div class="float-start"><?=$postweather['name'].",".$postweather['sys']['country']?></div>
                        <div class="float-end"><?=date("F d Y", $postweather['dt']);?></div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mx-auto"><h3 class="card-title text-center"><img src="https://openweathermap.org/img/wn/<?=$postweather['weather'][0]['icon']?>.png" alt="icon" class="image" width="50" height="50"/> <?=$postweather['main']['temp']."°С"?></h3></div>
                            <small>
                                <div class="mx-auto"><span class="align-middle"><?=ucwords($postweather['weather'][0]['description'])?></span></div>
                                <div class="float-start">Humidity: <?=$postweather['main']['humidity']." %"?></div></br>
                                <div class="float-start">Wind speed: <?=$postweather['wind']['speed']." km/h"?></div>
                            </small>   
                            </div>
                        <div class="row g-1"> 
                                <ul class="list-group">
                                <?php $date = date('Y-m-d 00:00:00', strtotime("+1 day"));?>
                                @foreach($postweatherhour['list'] as $value)
                                    @if($value['dt_txt'] < $date)
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="align-middle">{{date("ga", strtotime($value['dt_txt']))}}</span>
                                            <span class="align-middle"><img src="https://openweathermap.org/img/wn/{{$value['weather'][0]['icon']}}.png" alt="" class="icon"/>{{ucwords($value['weather'][0]['description'])}}</span>
                                            <span class="align-middle"><?=$value['main']['temp']."°С"?></span>
                                        </div>
                                    </li>
                                    @endif 
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
        @else
        <!--Default Weather-->
        <div class="row">
            @foreach($locarr as $location)
            <div class="col-sm-4">
                <div class="card" id="weather">
                    <div class="card-body">
                        <h6 class="card-title">Weather in {{$weatherCityName[$location]}}</h6>
                        <h4 class="card-text">{{$weatherTempCel[$location]}} °С</h4>
                        <div class="col-12 text-truncate"><img src="https://openweathermap.org/img/wn/{{$weatherIcon[$location]}}.png" alt="" class="icon"/>{{ucwords($weatherDesc[$location])}}</div>
                        <div class="col-12 text-truncate">Humidity: {{$weatherHumidity[$location]}}</div>
                        <div class="col-12 text-truncate">Wind speed: {{$weatherWindSpeed[$location]}}</div>
                    </div>
                </div>
                <br/>
            </div>
            @endforeach
        </div>
        @endif
        
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>