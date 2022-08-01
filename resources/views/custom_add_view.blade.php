<!-- First, extends to the CRUDBooster Layout -->
@extends('crudbooster::admin_template')
@section('content')
  <!-- Your html goes here -->
  <div class='panel panel-default'>
    
    <div class='panel-heading'>Cotizaciones</div>
    <div class='panel-body'>

        
          
      <form method='post' action='{{CRUDBooster::mainpath('add-save')}}'>
        <div class='form-group'>
          <label>Label 1</label>
          <input type='text' name='label1' required class='form-control'/>
        </div>
         
        
        <!-- etc .... -->
        
      </form>
    </div>
    <div class='panel-footer'>
      <input type='submit' class='btn btn-primary' value='Save changes'/>
    </div>
  </div>
@endsection