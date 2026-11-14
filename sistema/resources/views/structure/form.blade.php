<form method="POST" action="{{ route('structure.store', ['type' => $formType]) }}" class="structure-form" data-structure-form>
    @csrf
    <input type="hidden" name="region_id" value="{{ $region?->id }}">
    <fieldset @disabled(!$region && $formType !== 'region')>
        @if($formType !== 'rule')
        <x-ui.input name="name" label="Nome" :icon="match($formType) { 'region', 'unit' => 'building', 'classroom', 'level' => 'users', default => 'book' }" :value="old('name')" :error="$errors->first('name')" maxlength="120" required />
        <x-ui.input name="code" label="Código" :value="old('code')" :error="$errors->first('code')" hint="Identificação curta, com letras sem acentos e números. Ex.: SEMENTES-N1." maxlength="40" icon="hash" required />
        @endif

        @if($formType === 'region')
        <x-ui.select name="timezone" label="Fuso horário" icon="clock" :error="$errors->first('timezone')" required>
            @foreach(['America/Sao_Paulo' => 'Brasília', 'America/Manaus' => 'Manaus', 'America/Rio_Branco' => 'Rio Branco', 'America/Noronha' => 'Fernando de Noronha'] as $value => $label)<option value="{{ $value }}" @selected(old('timezone', 'America/Sao_Paulo') === $value)>{{ $label }} · {{ $value }}</option>@endforeach
        </x-ui.select>
        @endif

        @if(in_array($formType, ['edition', 'rule']))
        <x-ui.input name="version" label="Versão" type="number" min="1" max="32767" :value="old('version', 1)" :error="$errors->first('version')" required />
        @endif
        @if($formType === 'edition')
        <x-ui.textarea name="source_reference" label="Referência da edição" :error="$errors->first('source_reference')" hint="Fonte ou documento a conferir pela coordenação." maxlength="500" rows="3" optional>{{ old('source_reference') }}</x-ui.textarea>
        @endif

        @if(in_array($formType, ['level', 'module', 'rule', 'offering']))
        <x-ui.select name="curriculum_edition_id" label="Edição do currículo" icon="book" :error="$errors->first('curriculum_edition_id')" required data-edition-selector>
            <option value="">Selecione uma edição</option>
            @foreach($editions as $item)<option value="{{ $item->id }}" @selected(old('curriculum_edition_id') == $item->id)>{{ $item->name }} · v{{ $item->version }}</option>@endforeach
        </x-ui.select>
        @endif

        @if(in_array($formType, ['level', 'module']))
        <x-ui.input name="position" label="Ordem na edição" type="number" min="1" max="32767" :value="old('position')" :error="$errors->first('position')" required />
        @endif
        @if($formType === 'level')
        <div class="structure-form-row">
            <x-ui.input name="min_age" label="Idade mínima" type="number" min="0" max="120" :value="old('min_age')" :error="$errors->first('min_age')" required />
            <x-ui.input name="max_age" label="Idade máxima" type="number" min="0" max="120" :value="old('max_age')" :error="$errors->first('max_age')" required />
        </div>
        <p class="structure-form-hint">Faixas de uma mesma edição não podem se sobrepor. Uma edição em rascunho pode estar incompleta; não enquadra alunos.</p>
        @endif
        @if($formType === 'module')
        <x-ui.input name="lesson_count" label="Quantidade de lições" type="number" min="1" max="1000" :value="old('lesson_count')" :error="$errors->first('lesson_count')" required />
        @endif
        @if($formType === 'rule')
        <x-ui.alert tone="info" title="Preparação para revisão">Esta versão começa sem corte, transição, regra de 29/02 ou vigência. Essas decisões serão registradas e validadas antes da ativação.</x-ui.alert>
        @endif

        @if($formType === 'offering')
        <x-ui.select name="local_unit_id" label="Núcleo" icon="building" :error="$errors->first('local_unit_id')" required>
            <option value="">Selecione um núcleo</option>
            @foreach($units as $item)<option value="{{ $item->id }}" @selected(old('local_unit_id') == $item->id)>{{ $item->name }}</option>@endforeach
        </x-ui.select>
        <x-ui.select name="curriculum_module_id" label="Módulo" icon="book" :error="$errors->first('curriculum_module_id')" required data-edition-options>
            <option value="">Selecione um módulo da edição</option>
            @foreach($modules as $item)<option value="{{ $item->id }}" data-edition="{{ $item->curriculum_edition_id }}" @selected(old('curriculum_module_id') == $item->id)>{{ $item->name }} · {{ $item->edition->code }} v{{ $item->edition->version }}</option>@endforeach
        </x-ui.select>
        <x-ui.select name="pedagogical_rule_id" label="Regulamento em rascunho" icon="book" :error="$errors->first('pedagogical_rule_id')" required data-edition-options>
            <option value="">Selecione uma versão da edição</option>
            @foreach($rules as $item)<option value="{{ $item->id }}" data-edition="{{ $item->curriculum_edition_id }}" @selected(old('pedagogical_rule_id') == $item->id)>Versão {{ $item->version }} · {{ $item->edition->code }} v{{ $item->edition->version }}</option>@endforeach
        </x-ui.select>
        <div class="structure-form-row">
            <x-ui.input name="starts_on" label="Início previsto" type="date" :value="old('starts_on')" :error="$errors->first('starts_on')" required />
            <x-ui.input name="ends_on" label="Fim previsto" type="date" :value="old('ends_on')" :error="$errors->first('ends_on')" required />
        </div>
        <p class="structure-form-hint">Fuso herdado da regional: {{ $region?->timezone }}. As datas não registram início efetivo.</p>
        @endif

        @if($formType === 'classroom')
        <x-ui.select name="module_offering_id" label="Oferta e núcleo" icon="building" :error="$errors->first('module_offering_id')" required data-offering-selector>
            <option value="">Selecione uma oferta</option>
            @foreach($offerings as $item)<option value="{{ $item->id }}" data-edition="{{ $item->curriculum_edition_id }}" @selected(old('module_offering_id') == $item->id)>{{ $item->localUnit->name }} · {{ $item->name }} · {{ $item->code }} · {{ $item->starts_on->format('d/m/Y') }} · {{ $item->edition->code }} v{{ $item->edition->version }}</option>@endforeach
        </x-ui.select>
        <x-ui.select name="level_id" label="Nível da turma" :error="$errors->first('level_id')" required data-edition-options>
            <option value="">Selecione um nível da edição</option>
            @foreach($levels as $item)<option value="{{ $item->id }}" data-edition="{{ $item->curriculum_edition_id }}" @selected(old('level_id') == $item->id)>{{ $item->code }} · {{ $item->min_age }}–{{ $item->max_age }} anos · {{ $item->edition->code }} v{{ $item->edition->version }}</option>@endforeach
        </x-ui.select>
        <p class="structure-form-hint">A turma herda regional, núcleo e edição da oferta escolhida. O nome pode se repetir em outra unidade; cada turma tem identificação própria.</p>
        @endif
        @if(in_array($formType, ['offering', 'classroom']))
        <x-ui.input name="capacity" label="Capacidade proposta" type="number" min="1" max="10000" :value="old('capacity')" :error="$errors->first('capacity')" required />
        @endif
        <x-ui.button type="submit" icon="check">Salvar {{ mb_strtolower($formLabels[$formType]) }}</x-ui.button>
    </fieldset>
</form>
