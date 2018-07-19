@extends('base')

@section('content')

<div class="row justify-content-center">



    <div name="setPrice">
        <label>Выберете способ определения цены</label>
        <select class="custom-select" name="priceType">
            <?php
            foreach([1,2] as $i)
                echo '<option value="'.$i.'" '.(($_COOKIE["setPriceWay"]==$i)?"selected":"").'>Способ '.$i.'</option>';
            ?>
        </select>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Продукт</th>
                <th scope="col">Цена по умолчанию</th>
                <th scope="col">Текущая цена</th>
                <th scope="col">С</th>
                <th scope="col">По</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $prod)
                <tr data="{{ $prod->id }}">
                    <td data="id">{{ $prod->id }}</td>
                    <td>{{ $prod->name }}</td>
                    <td>{{ $prod->price }}</td>
                    <td>
                        <select class="custom-select" name="priceSelect">
                            <option value="default">Цена по умолчанию</option>
                            @foreach($discounts as $disc)
                                <option value="{{ $disc->id }}"  @if($prod->discount_id == $disc->id) selected @endif>{{ $disc->price }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td data="date_start">
                        @if($prod->discount_id != null)
                            {{ date("d.m.Y",$prod->discount->date_start) }}
                        @endif
                    </td>
                    <td data="date_end">
                        @if($prod->discount_id != null)
                            {{ date("d.m.Y",$prod->discount->date_end) }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
        <div>
            {!! $chart->container() !!}

            {!! $chart->script() !!}
        </div>

    </div>
</div>

@push('scripts')
    <script src="{{asset('js/setPrice.js')}}?<?php echo filemtime("js/setPrice.js")?>"></script>
@endpush

@endsection
