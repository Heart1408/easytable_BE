<?php

namespace App\Services\Staff;

use App\Repositories\Staff\tableRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class TableService
{
	protected $tableRepository;
	public function __construct(TableRepository $tableRepository)
	{
		$this->tableRepository = $tableRepository;
	}
	
}