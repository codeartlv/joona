@if (!empty($languages) && count($languages) > 1)
	<section class="card-auth__locale">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb breadcrumb-languages">
				@foreach ($languages as $language)
					<li class="breadcrumb-item {{$language['active'] ? 'active':''}}">
						<a href="{{$language['url']}}">
							{{$language['title']}}
						</a>
					</li>
				@endforeach
			</ol>
		</nav>
	</section>
@endif

<section class="card-auth__lightmode">
	<a href="javascript:;" data-dark-icon="dark_mode" data-light-icon="light_mode" data-bind="admin.colorThemeSwitch">
		@php
			$icon = $theme == 'dark' ? 'light_mode':'dark_mode';
		@endphp

		@icon($icon)
	</a>
</section>