<x-layout>
    <div class="cell-wrapper">
        <div class="cell-wrapper__caption">
            <p class="caps">{{ $data['title'] }}</p>
            <p class="caps">{{ $data['pipeline'] }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    @foreach($data['col'] as $k => $v)
                        <th scope="col">{{ $v }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['count'] }}</td>
                        <td>{{ $item['price'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    @foreach($data['col'] as $k => $v)
                        <td><b>Всего:</b></td>
                        <td><b>{{ $size[$k] }}</b></td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</x-layout>
