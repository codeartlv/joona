<?php

namespace Codeart\Joona\Enums;

/**
 * File upload status enumeration.
 */
enum UploadStatus: string
{
	case SUCCESS = 'success';
	case FAILED = 'failed';
}
