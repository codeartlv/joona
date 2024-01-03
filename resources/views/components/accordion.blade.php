@php
	$accorionId = 'acc'.uniqid();
@endphp

<div class="accordion" id="{{$accorionId}}">
	@foreach ($items as $item)
		@php
			$tabId = 'tab'.uniqid();
		@endphp
		<div class="accordion-item">
    		<h2 class="accordion-header">
      			<button class="accordion-button {{$item[2] ? '':'collapsed'}}" type="button" data-bs-toggle="collapse" data-bs-target="#{{$tabId}}" aria-expanded="{{$item[2] ? 'false':'true'}}" aria-controls="collapseOne">
        			{{$item[0]}}
      			</button>
    		</h2>
    		<div id="{{$tabId}}" class="accordion-collapse collapse {{$item[2] ? 'show':''}}" {{$autocollapse ? 'data-bs-parent="#'.$accorionId.'"':''}}>
      			<div class="accordion-body">
        			{{$item[1]}}
      			</div>
    		</div>
  		</div>
	@endforeach
</div>
