<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
  /**
   * Display the contact form.
   */
  public function load()
  {
    return Inertia::render('frontend/contact/Index');
  }
}
