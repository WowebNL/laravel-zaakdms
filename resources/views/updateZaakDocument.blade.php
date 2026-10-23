@extends('zaakdms::layouts.master')
@section('body')
<zkn:updateZaakdocument_Di02 
        xmlns:stuf="http://www.egem.nl/StUF/StUF0301" 
        xmlns:xlink="http://www.w3.org/1999/xlink" 
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"  
        xmlns:zkn="http://www.egem.nl/StUF/sector/zkn/0310" 
        xmlns:BG="http://www.egem.nl/StUF/sector/bg/0310" 
        xmlns:gml="http://www.opengis.net/gml">
    <zkn:stuurgegevens>
        <stuf:berichtcode>Di02</stuf:berichtcode>
        <x-zaakdms-zender />
        <x-zaakdms-ontvanger />
        @if(isset($identificatie))
        <stuf:referentienummer>{{$identificatie}}</stuf:referentienummer>
        @endif
        <stuf:tijdstipBericht>{{\Woweb\Zaakdms\Helper::now()->format('YmdHis')}}</stuf:tijdstipBericht>
        <stuf:functie>updateZaakdocument</stuf:functie>
    </zkn:stuurgegevens>
    <zkn:parameters>
        <zkn:checkedOutId>0</zkn:checkedOutId>
    </zkn:parameters>
    <zkn:edcLk02 stuf:entiteittype="EDC" stuf:functie="update">
        <zkn:parameters>
            <stuf:mutatiesoort>T</stuf:mutatiesoort>
        </zkn:parameters>
        <zkn:object stuf:entiteittype="EDC" stuf:verwerkingssoort="T">
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
            @if(isset($extension))
                <zkn:formaat>.{{$extension}}</zkn:formaat>
            @endif
            <zkn:taal>nl</zkn:taal>
            <zkn:vertrouwelijkAanduiding>{{$vertrouwelijkAanduiding}}</zkn:vertrouwelijkAanduiding>
            <zkn:inhoud stuf:bestandsnaam="{{$bestandsnaam}}">{{$file_base64}}</zkn:inhoud>
            <zkn:isRelevantVoor stuf:entiteittype="EDCZAK" stuf:verwerkingssoort="I">
                <zkn:gerelateerde stuf:entiteittype="ZAK" stuf:verwerkingssoort="I">
                    <zkn:identificatie>{{$zaak['identificatie']}}</zkn:identificatie>
                </zkn:gerelateerde>
            </zkn:isRelevantVoor>
        </zkn:object>
    </zkn:edcLk02>
</zkn:updateZaakdocument_Di02>
@stop