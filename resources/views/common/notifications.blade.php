@foreach($notifications as $notification)
	<div class="notification notification--{{$notification->presenter->getTypeIdentifier()}} {{$notification->read ? '' : 'unread'}}" data-role="notification" data-id="{{$notification->id}}">
		<figure>
			<span>
				<img src="{{$notification->getImage()}}" />
			</span>
		</figure>
		<a href="{{$notification->presenter->getUrl()}}">
			<header>
				<h4>
					{{$notification->presenter->getTitle()}}

					@if($notification->isGlobal)
						@icon('globe')
					@endif
				</h4>
				<time>{{$notification->createdAt->format('d.m.Y H:i')}}</time>
			</header>

			<p>{{$notification->presenter->getMessage()}}</p>

		</a>
	</div>
@endforeach