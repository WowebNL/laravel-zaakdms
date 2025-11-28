@extends('zaakdms::layouts.master')
@section('body')
<zkn:edcLk01 xmlns:zkn="http://www.egem.nl/StUF/sector/zkn/0310"
xmlns:stuf="http://www.egem.nl/StUF/StUF0301">
    <zkn:stuurgegevens>
        <stuf:berichtcode>Lk01</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        @if(isset($identificatie))
        <stuf:referentienummer>{{$identificatie}}</stuf:referentienummer>
        @endif
        <stuf:tijdstipBericht>{{\Carbon\Carbon::now()->format('Ymdhis')}}</stuf:tijdstipBericht>
        <stuf:entiteittype>EDC</stuf:entiteittype>
    </zkn:stuurgegevens>
    <zkn:parameters>
        <stuf:mutatiesoort>T</stuf:mutatiesoort>
        <stuf:indicatorOvername>I</stuf:indicatorOvername>
    </zkn:parameters>
    <zkn:object stuf:verwerkingssoort="T" stuf:entiteittype="EDC">
        <zkn:identificatie>{{$identificatie}}</zkn:identificatie>
        <zkn:dct.omschrijving>{{$documenttype}}</zkn:dct.omschrijving>
        <zkn:creatiedatum>{{$creatiedatum}}</zkn:creatiedatum>
        @if(isset($ontvangstdatum))
        <zkn:ontvangstdatum>{{$ontvangstdatum}}</zkn:ontvangstdatum>
        @endif
        @if(isset($verzenddatum))
        <zkn:verzenddatum>{{$verzenddatum}}</zkn:verzenddatum>
        @endif
        @if(isset($beschrijving))
        <zkn:beschrijving>{{$beschrijving}}</zkn:beschrijving>
        @endif
        @if(isset($status))
        <zkn:status>{{$status}}</zkn:status>
        @endif
        <zkn:titel>{{$titel}}</zkn:titel>
        <zkn:formaat>{{$extension}}</zkn:formaat>
        <zkn:taal>nl</zkn:taal>
        <zkn:vertrouwelijkAanduiding>{{$vertrouwelijkAanduiding}}</zkn:vertrouwelijkAanduiding>
        <zkn:auteur>{{$auteur}}</zkn:auteur>
        <zkn:inhoud stuf:bestandsnaam="{{$bestandsnaam}}">{{$file_base64}}</zkn:inhoud>
        <zkn:isRelevantVoor stuf:entiteittype="EDCZAK" stuf:verwerkingssoort="I">
            <zkn:gerelateerde stuf:entiteittype="ZAK" stuf:verwerkingssoort="I">
                <zkn:identificatie>{{$zaak['identificatie']}}</zkn:identificatie>
            </zkn:gerelateerde>
        </zkn:isRelevantVoor>
    </zkn:object>
</zkn:edcLk01>
@stop