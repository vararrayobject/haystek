@extends('layouts.app')
@section('content')
<div class="w-100 m-auto pt-5 ">
    <div class="d-flex m-auto pb-4 w-75 ">
        <div class="w-50">
            <h1 class="head-text">PEOPLE DATA</h1>
        </div>
        <div class="w-50 d-flex justify-content-end">
            <button class="btn-cust" id="next-person" onclick="nextPerson();">NEXT PERSON</button>
        </div>
    </div>

    <div id="wrapper">
        @foreach ($chunkPeoples as $key => $people)
        <div class="d-flex mt-auto mr-auto ml-auto list-item-cust w-75 mb-3 ">
            <div class="w-10per  serial-text d-flex justify-content-center align-content-center ">
                <h1 class="my-auto">{{ $key + 1 }}</h1>
            </div>
            <div class="detail-text w-90per">
                <div class="w-100 name-div">
                    <h3 class="my-auto p-2">Name:<span>{{ $people->name }}</span></h3>
                </div>
                <div class="w-100 location-div">
                    <h3 class="my-auto p-2">Location:<span>{{ $people->location }}</span></h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex mt-auto mr-auto ml-auto w-75 mb-3 ">
        <h3 class="mx-auto p-2 total-people">CURRENTLY <span id="count">{{ $chunkPeoples->count() }}</span> PEOPLE
            SHOWING</h3>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let pageCounter = 1;
        // ajaxCall(pageCounter)

        function nextPerson() {
            ajaxCall(pageCounter)
        }

        function ajaxCall(nextCounter) {
            $.ajax({
                url: '/?page=' + nextCounter,
                dataType: 'json',

                success: function (result) {
                    if (Object.keys(result).length) {
                        $('#wrapper').empty();
                        $('#count').html(Object.keys(result).length);
                        $.each(result, function (key, value) {
                            let $div = `<div class="d-flex mt-auto mr-auto ml-auto list-item-cust w-75 mb-3 ">
                                <div class="w-10per  serial-text d-flex justify-content-center align-content-center ">
                                    <h1 class="my-auto">` + (parseInt(key) + 1) + `</h1>
                                </div>
                                <div class="detail-text w-90per">
                                    <div class="w-100 name-div">
                                        <h3 class="my-auto p-2">Name:<span>` + value.name + `</span></h3>
                                    </div>
                                    <div class="w-100 location-div">
                                        <h3 class="my-auto p-2">Location:<span>` + value.location + `</span></h3>
                                    </div>
                                </div>
                            </div>`
                            $('#wrapper').append($div);
                        });
                    } else alert("No more people!")
                    pageCounter++;
                },

                error: function (err) {
                    console.log(err)
                }
            })
        }
    </script>
@endsection