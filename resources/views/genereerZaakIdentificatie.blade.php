@extends('zaakdms::layouts.master')
@section('body')
 <zkn:genereerZaakIdentificatie_Di02>
    <zkn:stuurgegevens>
        <stuf:berichtcode>Di02</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        <stuf:functie>genereerZaakidentificatie</stuf:functie>
    </zkn:stuurgegevens>
</zkn:genereerZaakIdentificatie_Di02>
@stop