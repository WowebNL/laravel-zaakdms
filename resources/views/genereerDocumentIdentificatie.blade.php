@extends('zaakdms::layouts.master')
@section('body')
 <zkn:genereerDocumentIdentificatie_Di02>
    <zkn:stuurgegevens>
        <stuf:berichtcode>Di02</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        <stuf:functie>genereerDocumentidentificatie</stuf:functie>
    </zkn:stuurgegevens>
</zkn:genereerDocumentIdentificatie_Di02>
@stop