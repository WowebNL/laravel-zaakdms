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
        <stuf:tijdstipBericht>{{\Carbon\Carbon::now()->format('YmdHis')}}</stuf:tijdstipBericht>
        <stuf:entiteittype>ZAK</stuf:entiteittype>
    </zkn:stuurgegevens>
    <zkn:parameters>
        <stuf:mutatiesoort>T</stuf:mutatiesoort>
        <stuf:indicatorOvername>V</stuf:indicatorOvername>
    </zkn:parameters>
    <zkn:object stuf:entiteittype="ZAK" stuf:verwerkingssoort="T">
        @if(isset($identificatie))
        <zkn:identificatie>{{$identificatie}}</zkn:identificatie>
        @endif
        <zkn:omschrijving>{{$omschrijving}}</zkn:omschrijving>
        <zkn:startdatum>{{$startdatum}}</zkn:startdatum>
        <zkn:registratiedatum>{{$registratiedatum}}</zkn:registratiedatum>
        <zkn:zaakniveau>{{$zaakniveau}}</zkn:zaakniveau>
        <zkn:deelzakenIndicatie>N</zkn:deelzakenIndicatie>
        <zkn:isVan stuf:entiteittype="ZAKZKT" stuf:verwerkingssoort="T">
            <zkn:gerelateerde stuf:entiteittype="ZKT" stuf:verwerkingssoort="I">
                <zkn:omschrijving>{{$zaaktype['omschrijving']}}</zkn:omschrijving>
                <zkn:code>{{$zaaktype['code']}}</zkn:code>
            </zkn:gerelateerde>
        </zkn:isVan>
        @if($initiator['type'] == 'natuurlijk_persoon')
            <zkn:heeftAlsInitiator stuf:entiteittype="ZAKBTRINI" stuf:verwerkingssoort="T">
                <zkn:gerelateerde>
                    <zkn:natuurlijkPersoon stuf:verwerkingssoort="T" stuf:entiteittype="NPS">
                        @if(isset($initiator['natuurlijk_persoon']['bsn']) && $initiator['natuurlijk_persoon']['bsn'])
                            <bg:inp.bsn>{{$initiator['natuurlijk_persoon']['bsn']}}</bg:inp.bsn>
                        @endif
                        @if(isset($initiator['natuurlijk_persoon']['identificatie']) && $initiator['natuurlijk_persoon']['identificatie'])
                            <bg:anp.identificatie>{{$initiator['natuurlijk_persoon']['identificatie']}}</bg:anp.identificatie>
                        @endif
                        <bg:geslachtsnaam>{{$initiator['natuurlijk_persoon']['geslachtsnaam']}}</bg:geslachtsnaam>
                    </zkn:natuurlijkPersoon>
                </zkn:gerelateerde>
            </zkn:heeftAlsInitiator>
        @elseif($initiator['type'] == 'niet_natuurlijk_persoon')
            <zkn:heeftAlsInitiator stuf:entiteittype="ZAKBTRINI" stuf:verwerkingssoort="T">
                <zkn:gerelateerde>
                    <zkn:nietNatuurlijkPersoon stuf:verwerkingssoort="T" stuf:entiteittype="NNP">
                            @if(isset($initiator['niet_natuurlijk_persoon']) && isset($initiator['niet_natuurlijk_persoon']['nnpId']) && strlen($initiator['niet_natuurlijk_persoon']['nnpId']) == 9)
                                <bg:inn.nnpId>{{$initiator['niet_natuurlijk_persoon']['nnpId']}}</bg:inn.nnpId>
                            @else 
                                <bg:inn.nnpId>000000000</bg:inn.nnpId> 
                            @endif
                            <bg:authentiek stuf:metagegeven="true">N</bg:authentiek>
                            @if(isset($initiator['niet_natuurlijk_persoon']) && isset($initiator['niet_natuurlijk_persoon']['statutaireNaam']))
                                <bg:statutaireNaam>{{$initiator['niet_natuurlijk_persoon']['statutaireNaam']}}</bg:statutaireNaam>
                            @endif
                    </zkn:nietNatuurlijkPersoon>
                </zkn:gerelateerde>
            </zkn:heeftAlsInitiator>
        @endif
    </zkn:object>
</zkn:zakLk01>
@stop
