@extends('zaakdms::layouts.master')
@section('body')
<zkn:zakLk01 xmlns:zkn="http://www.egem.nl/StUF/sector/zkn/0310"
    xmlns:stuf="http://www.egem.nl/StUF/StUF0301" xmlns:bg="http://www.egem.nl/StUF/sector/bg/0310"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <zkn:stuurgegevens>
        <stuf:berichtcode>Lk01</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        @if(isset($identificatie))
        <stuf:referentienummer>{{$identificatie}}</stuf:referentienummer>
        @endif
        <stuf:tijdstipBericht>{{\Woweb\Zaakdms\Helper::now()->format('YmdHis')}}</stuf:tijdstipBericht>
        <stuf:entiteittype>ZAK</stuf:entiteittype>
    </zkn:stuurgegevens>
    <zkn:parameters>
        <stuf:mutatiesoort>T</stuf:mutatiesoort>
        <stuf:indicatorOvername>V</stuf:indicatorOvername>
    </zkn:parameters>
    <zkn:object stuf:entiteittype="ZAK" stuf:verwerkingssoort="T">
        <zkn:identificatie>{{$identificatie}}</zkn:identificatie>
        <zkn:resultaat>
        	<zkn:omschrijving>{{$resultaat}}</zkn:omschrijving>
        </zkn:resultaat>
        <zkn:einddatum>{{$einddatum}}</zkn:einddatum>
    </zkn:object>
</zkn:zakLk01>
@stop