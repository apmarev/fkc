<x-layout>
    <div class="cell-wrapper">
        <div class="cell-wrapper__caption">
            <p class="caps">Продажи по менеджерам</p>
            <p class="caps">Клиенты в активной работе</p>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">Менеджер</th>
                <th scope="col">Кол-во</th>
                <th scope="col">Сумма</th>
                <th scope="col">Бюджет</th>
            </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['count'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td>{{ $item['budget'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><b>Всего:</b></td>
                    <td><b>{{ $size['count'] }}</b></td>
                    <td><b>{{ $size['price'] }}</b></td>
                    <td><b>{{ $size['budget'] }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</x-layout>
