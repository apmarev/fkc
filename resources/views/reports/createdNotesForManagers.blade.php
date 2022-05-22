<x-layout>
    <div class="cell-wrapper">
        <div class="cell-wrapper__caption">
            Создано примечаний по менеджерам (Клиенты в активной работе )
        </div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">Источник</th>
                <th scope="col">Кол-во</th>
            </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['count'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><b>Всего:</b></td>
                    <td><b>{{ $size['count'] }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</x-layout>
