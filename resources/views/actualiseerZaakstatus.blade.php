@extends('zaakdms::layouts.master')
@section('body')
<zkn:zakLk01 xmlns:zds="http://www.stufstandaarden.nl/koppelvlak/zds0120"
    xmlns:zkn="http://www.egem.nl/StUF/sector/zkn/0310"
    xmlns:stuf="http://www.egem.nl/StUF/StUF0301">
    <zkn:stuurgegevens>
        <stuf:berichtcode>Lk01</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        <stuf:referentienummer>{{$identificatie}}</stuf:referentienummer>
        <stuf:tijdstipBericht>{{\Carbon\Carbon::now()->format('Ymdhis')}}</stuf:tijdstipBericht>
        <stuf:entiteittype>ZAK</stuf:entiteittype>
    </zkn:stuurgegevens>
    <zkn:parameters>
        <stuf:mutatiesoort>T</stuf:mutatiesoort>
        <stuf:indicatorOvername>V</stuf:indicatorOvername>
    </zkn:parameters>
    <zkn:object stuf:entiteittype="ZAK" stuf:verwerkingssoort="W">
        <zkn:identificatie>{{$identificatie}}</zkn:identificatie>
        <zkn:heeft stuf:verwerkingssoort="I" stuf:entiteittype="ZAKSTT">
            <zkn:gerelateerde stuf:entiteittype="STT" stuf:verwerkingssoort="W">
                <zkn:volgnummer>{{$volgnummer}}</zkn:volgnummer>
                <zkn:omschrijving>{{$omschrijving}}</zkn:omschrijving>
            </zkn:gerelateerde>
            <zkn:datumStatusGezet>{{\Carbon\Carbon::now()->format('Ymdhis')}}</zkn:datumStatusGezet>
            <zkn:isGezetDoor  stuf:verwerkingssoort="I" stuf:entiteittype="ZAKSTTBTR">
                <zkn:gerelateerde><zkn:medewerker stuf:entiteittype="MDW" stuf:verwerkingssoort="I">
                    <zkn:identificatie>{{$gebruikerIdentificatie}}</zkn:identificatie>
                </zkn:medewerker></zkn:gerelateerde>
            </zkn:isGezetDoor>
        </zkn:heeft>
    </zkn:object>
</zkn:zakLk01>
@stop