<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWizardRequest;
use Illuminate\Http\Request;

class WizardController extends Controller
{
    public function store(StoreWizardRequest $request)
    {
        $request->validated();



    }
}
