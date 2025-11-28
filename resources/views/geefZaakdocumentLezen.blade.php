@extends('zaakdms::layouts.master')
@section('body')
<zkn:edcLv01
xmlns:stuf="http://www.egem.nl/StUF/StUF0301"
xmlns:zkn="http://www.egem.nl/StUF/sector/zkn/0310"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<zkn:stuurgegevens>
    <stuf:berichtcode>Lv01</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        @if(isset($identificatie))
        <stuf:referentienummer>{{$identificatie}}</stuf:referentienummer>
        @endif
        <stuf:tijdstipBericht>{{\Carbon\Carbon::now()->format('Ymdhis')}}</stuf:tijdstipBericht>
        <stuf:entiteittype>EDC</stuf:entiteittype>
    </zkn:stuurgegevens>
    <zkn:parameters>
        <stuf:sortering>0</stuf:sortering>
        <stuf:indicatorVervolgvraag>false</stuf:indicatorVervolgvraag>
    </zkn:parameters>
    <zkn:gelijk stuf:entiteittype="EDC">
        <zkn:identificatie>{{$identificatie}}</zkn:identificatie>
    </zkn:gelijk>
    <zkn:scope>
        <zkn:object stuf:entiteittype="EDC" stuf:scope="alles"/>
    </zkn:scope>
</zkn:edcLv01>
@stop