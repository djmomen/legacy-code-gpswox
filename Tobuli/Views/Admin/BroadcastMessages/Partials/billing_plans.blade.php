
{!! Form::label('receivers[billing_plans][]', trans('admin.billing_plan'), ['class' => 'control-label"']) !!}
{!! Form::select('receivers[billing_plans][]', $billingPlans, null, ['multiple' => 'multiple', 'class' => 'form-control', 'data-filter' => 'true']) !!}
