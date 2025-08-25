<div class="uploader uploader--{{$class}}" data-bind="components.uploader" {{$attributes}}>
	<label data-role="trigger" class="upload-area uploader__item">
		<input type="file" name="{{$name}}" />
		<figcaption>
			@icon('upload')
			<em>{{__('joona::common.select_files')}}</em>
		</figcaption>
	</label>
	<script data-role="data" type="application/ld+json">@json($files)</script>
	
	<template data-role="thumbnail">
		<div class="upload-file uploader__item" data-id="0">
			<figure data-role="thumbnail">
				<div class="dropdown upload-file__menu">
					<a href="javascript:;" class="btn btn-sm btn-secondary captionless" data-bs-toggle="dropdown">
						@icon('more_horiz')
					</a>
					<div class="dropdown-menu" data-role="menu">

					</div>
				</div>
				<div class="upload-file__progress progress">
					<div class="progress-bar bg-success" style="width:0%" data-role="progress"></div>
				</div>
				<div class="upload-file__msg" data-role="message"></div>
			</figure>
			<div class="upload-file__info">
				<span class="upload-file__icon fiv-sqo" data-role="file-icon"></span>
				<span class="upload-file__filename" data-role="filename"></span>
			</div>
			<div class="upload-file__caption">
				<x-input type="text" size="sm" placeholder="{{__('joona::common.image_caption')}}" data-role="caption" />
			</div>
		</div>
	</template>

	{{ $slot }}
</div>
