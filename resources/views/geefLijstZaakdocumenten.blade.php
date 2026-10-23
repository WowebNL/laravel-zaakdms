@extends('zaakdms::layouts.master')
@section('body')
<zkn:zakLv01
    xmlns:zkn="http://www.egem.nl/StUF/sector/zkn/0310"
    xmlns:stuf="http://www.egem.nl/StUF/StUF0301">
    <zkn:stuurgegevens>
        <stuf:berichtcode>Lv01</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        @if(isset($identificatie))
        <stuf:referentienummer>{{$identificatie}}</stuf:referentienummer>
        @endif
        <stuf:tijdstipBericht>{{\Woweb\Zaakdms\Helper::now()->format('YmdHis')}}</stuf:tijdstipBericht>
        <stuf:entiteittype>ZAK</stuf:entiteittype>
    </zkn:stuurgegevens>
    <zkn:parameters>
        <stuf:sortering>0</stuf:sortering>
        <stuf:indicatorVervolgvraag>false</stuf:indicatorVervolgvraag>
    </zkn:parameters>
    <zkn:gelijk stuf:entiteittype="ZAK" >
        <zkn:identificatie>{{$identificatie}}</zkn:identificatie>
    </zkn:gelijk>
</zkn:zakLv01>
@stop