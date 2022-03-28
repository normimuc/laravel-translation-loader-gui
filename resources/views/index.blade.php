@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white text-dark">
                        @if(isset($group)) Translation lines of ({{ $group }}) group @endif
                    </div>
                    <div class="card-body">
                        @if(count($groups))
                            <div class="row justify-content-center">
                                <select class="form-control col-md-9 mr-3" id="group-select">
                                    @foreach($groups as $itergroup)
                                        <option value="{{ $itergroup }}" @if(isset($group) && $group == $itergroup) selected @endif>{{ $itergroup }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary col-md-2" onclick="loadContent()">
                                    Show Group Lines
                                </button>
                            </div>
                        @else
                            <div class="row justify-content-center">
                                <div class="col-md-6 col-md-offset-3 alert alert-info text-center">
                                    <p>
                                        Please run the command <pre>php artisan translations:load</pre>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Key</th>
                                @foreach (($locales = config('translation-loader-gui.locales')) as $lang)
                                    <th>{{ $lang }}</th>
                                @endforeach
                            </tr>
                            @if(isset($group))
                            @foreach ($lines as $line)
                                <tr>
                                    <td>
                                        {{ $line->key }}
                                    </td>
                                    @foreach($locales as $lang)
                                        @if(isset($line->text[$lang]))
                                            <td id="{{ $lang . $line->id }}">
                                                <a href="javascript:" data-toggle="modal" data-target="#exampleModal{{ $line->id . $lang }}">
                                                    {{ $line->text[$lang] }}
                                                    <i class="ion-edit"></i>
                                                </a>
                                                <div class="modal fade" id="exampleModal{{ $line->id . $lang }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">{{ $line->key }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form>
                                                                <div class="form-group">
                                                                    <input type="hidden" name="lang" value="{{ $lang }}">
                                                                    <textarea class="form-control" id="trans{{ $lang }}{{ $line->id }}" name="trans">{{ $line->text[$lang] }}</textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button type="button"
                                                                            onclick="controlTrans('{{ route('_translation-loader.update', $line) }}', '{{ $lang }}', '{{ $line->id }}')"
                                                                            class="btn btn-primary"
                                                                            data-dismiss="modal">
                                                                        Save changes
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @else
                                            <td id="{{ $lang . $line->id }}">
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#exampleModal{{ $line->id . $lang }}">
                                                    add <i class="ion-plus-round"></i>
                                                </button>
                                                <div class="modal fade" id="exampleModal{{ $line->id . $lang }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">{{ $line->key }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea class="form-control" id="trans{{ $lang }}{{ $line->id }}"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="button"
                                                                        onclick="controlTrans('{{ route('_translation-loader.update', $line) }}', '{{ $lang }}', '{{ $line->id }}')"
                                                                        class="btn btn-primary"
                                                                        data-dismiss="modal">Save changes</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


    <script>
        function controlTrans(url, lang, id, method) {
            let trans = document.getElementById(`trans${lang+id}`);

            const req = {
                _token: '{{ csrf_token() }}',
                lang: lang,
                trans: trans.value
            };

            $.ajax({
                url: url,
                method: 'put',
                data: req
            }).done((data) => {
               let el = document.getElementById(lang + id);
               el.innerHTML = data;
            });
        }

        function loadContent() {
            let group = document.getElementById('group-select').value;

            window.location.search = `group=${group}`;
        }
    </script>

@endsection
