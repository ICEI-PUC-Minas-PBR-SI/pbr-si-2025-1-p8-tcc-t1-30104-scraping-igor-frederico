<div class="space-y-10 p-4">
    <div>
        <h2 class="text-xl font-bold mb-2">No período de coleta considerado, qual categoria recebeu o maior número de
            patentes publicadas?
        </h2>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Fármaco/Doença</th>
                    <th class="px-4 py-2">Total de Patentes</th>
                </tr>
            </thead>
            @php
                $max = $data['por_categoria']->max('total');
                $min = $data['por_categoria']->min('total');
            @endphp

            <tbody>
                @foreach ($data['por_categoria'] as $row)
                    @php
                        $bg = '';
                        if ($row->total === $max) {
                            $bg = 'bg-green-200'; // mais registros
                        } elseif ($row->total === $min) {
                            $bg = 'bg-red-200'; // menos registros
                        }
                    @endphp
                    <tr class="{{ $bg }}">
                        <td class="border px-4 py-2">{{ $row->farmaco }}</td>
                        <td class="border px-4 py-2">{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2 class="text-xl font-bold mb-2">No período de coleta considerado, qual fármaco recebeu o maior número de
            patentes publicadas?
        </h2>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Fármaco</th>
                    <th class="px-4 py-2">Total de Patentes</th>
                </tr>
            </thead>
            @php
                $max = $data['por_farmaco']->max('total');
                $min = $data['por_farmaco']->min('total');
            @endphp

            <tbody>
                @foreach ($data['por_farmaco'] as $row)
                    @php
                        $bg = '';
                        if ($row->total === $max) {
                            $bg = 'bg-green-200'; // mais registros
                        } elseif ($row->total === $min) {
                            $bg = 'bg-red-200'; // menos registros
                        }
                    @endphp
                    <tr class="{{ $bg }}">
                        <td class="border px-4 py-2">{{ $row->farmaco }}</td>
                        <td class="border px-4 py-2">{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2 class="text-xl font-bold mb-2">No período de coleta considerado, qual doença recebeu o maior número de
            patentes publicadas?</h2>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Doença</th>
                    <th class="px-4 py-2">Total de Patentes</th>
                </tr>
            </thead>
            @php
                $max = $data['por_doenca']->max('total');
                $min = $data['por_doenca']->min('total');
            @endphp

            <tbody>
                @foreach ($data['por_doenca'] as $row)
                    @php
                        $bg = '';
                        if ($row->total === $max) {
                            $bg = 'bg-green-200'; // mais registros
                        } elseif ($row->total === $min) {
                            $bg = 'bg-red-200'; // menos registros
                        }
                    @endphp
                    <tr class="{{ $bg }}">
                        <td class="border px-4 py-2">{{ $row->farmaco }}</td>
                        <td class="border px-4 py-2">{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Seção de Patentes por Dia -->
    <div>
        <h2 class="text-xl font-bold mb-2">No período de coleta considerado, qual dia recebeu o maior número de patentes
            publicadas?</h2>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Data</th>
                    <th class="px-4 py-2">Total</th>
                </tr>
            </thead>
            @php
                $max = $data['por_dia']->max('total');
                $min = $data['por_dia']->min('total');
            @endphp

            <tbody>
                @foreach ($data['por_dia'] as $row)
                    @php
                        $bg = '';
                        if ($row->total === $max) {
                            $bg = 'bg-green-200'; // mais registros
                        } elseif ($row->total === $min) {
                            $bg = 'bg-red-200'; // menos registros
                        }
                    @endphp
                    <tr class="{{ $bg }}">
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->date_published)->format('d/m/Y') }}
                        </td>
                        <td class="border px-4 py-2">{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Seção de Patentes por Semana -->
    <div>
        <h2 class="text-xl font-bold mb-2">
            No período de coleta considerado, qual semana recebeu o maior número de patentes publicadas?
        </h2>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Semana</th>
                    <th class="px-4 py-2">Total</th>
                </tr>
            </thead>

            @php
                $max = $data['por_semana']->max('total');
                $min = $data['por_semana']->min('total');

                function getBgColor($valor, $min, $max)
                {
                    $percent = $max - $min > 0 ? ($valor - $min) / ($max - $min) : 0;

                    if ($percent >= 0.9) {
                        return 'bg-green-300'; // topo
                    } elseif ($percent >= 0.7) {
                        return 'bg-green-100';
                    } elseif ($percent >= 0.5) {
                        return 'bg-yellow-100';
                    } elseif ($percent >= 0.3) {
                        return 'bg-orange-100';
                    } elseif ($percent > 0) {
                        return 'bg-red-100';
                    } else {
                        return 'bg-red-300'; // fundo
                    }
                }
            @endphp

            <tbody>
                @foreach ($data['por_semana'] as $row)
                    @php
                        $ano = substr($row->semana, 0, 4);
                        $semanaNumero = (int) substr($row->semana, 4);
                        $ordinales = ['Primeira', 'Segunda', 'Terceira', 'Quarta', 'Quinta'];

                        $semanaTexto = $ordinales[$semanaNumero - 1] ?? "Semana $semanaNumero";
                        $bg = getBgColor($row->total, $min, $max);
                    @endphp
                    <tr class="{{ $bg }}">
                        <td class="border px-4 py-2">{{ $semanaTexto }} semana de janeiro de {{ $ano }}</td>
                        <td class="border px-4 py-2">{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



    <!-- Seção de Top 10 Inventores -->
    <div>
        <h2 class="text-xl font-bold mb-2">No período de coleta considerado, qual inventor publicou o maior número de
            patentes?</h2>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Inventor</th>
                    <th class="px-4 py-2">Total de Patentes</th>
                </tr>
            </thead>
            @php
                $max = $data['por_inventor']->max('total');
                $min = $data['por_inventor']->min('total');
            @endphp
            <tbody>
                @foreach ($data['por_inventor'] as $row)
                    @php
                        $bg = '';
                        if ($row->total === $max) {
                            $bg = 'bg-green-200'; // mais registros
                        } elseif ($row->total === $min) {
                            $bg = 'bg-red-200'; // menos registros
                        }
                    @endphp
                    <tr class="{{ $bg }}">
                        <td class="border px-4 py-2">{{ $row->name }}</td>
                        <td class="border px-4 py-2">{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
