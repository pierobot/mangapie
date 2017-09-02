@extends ('layout')

@section ('title')
:: Settings
@endsection

@section ('content')

   <div class="panel panel-default">

       <div class="panel-heading">
           <h2 class="panel-title">Settings</h2>
       </div>

       <div class="panel-body">

           <ul class="list-group">
               <li class="list-group-item">

                   {{ Form::open(['action' => 'UserSettingsController@update']) }}

                   <div class="row">
                       <div class="form-group col-xs-12 col-lg-3">

                           {{ Form::label('theme:', null, ['for' => 'theme']) }}
                           {{-- {{ Form::select('theme', $theme_collections, '', ['class' => 'form-control']) }} --}}
                           {{-- TO-DO: I couldn't figure out how to write the equivalent of the line below to the above --}}
                           <select name="theme" class="form-control">
                               <option disabled="disabled" selected="selected">{{ $current_theme }}</option>
                           @foreach ($theme_collections as $collection_name => $theme)
                               <optgroup label="{{ $collection_name }}">

                                   @foreach ($theme as $theme_name => $theme_path)
                                       <option value="{{ $collection_name . '/' . $theme_name }}">{{ $theme_name }}</option>
                                   @endforeach

                               </optgroup>
                           @endforeach
                           </select>

                       </div>
                   </div>

                   <div class="row">
                       <div class="form-group col-xs-12 col-lg-3">
                       {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                       </div>
                   </div>

                   {{ Form::close() }}

               </li>
           </ul>

       </div>

   </div>

@endsection
