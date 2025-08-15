@if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="row">
  <div class="mb-3 col-md-6">
    <label for="full_name" class="form-label">Nombre Completo</label>
    <input class="form-control" type="text" id="full_name" name="full_name"
      value="{{ old('full_name', $rider->full_name ?? '') }}" autofocus />
  </div>
  <div class="mb-3 col-md-6">
    <label for="dni" class="form-label">DNI</label>
    <input class="form-control" type="text" name="dni" id="dni"
      value="{{ old('dni', $rider->dni ?? '') }}" />
  </div>
  <div class="mb-3 col-md-6">
    <label for="email" class="form-label">E-mail</label>
    <input class="form-control" type="email" id="email" name="email"
      value="{{ old('email', $rider->email ?? '') }}" placeholder="john.doe@example.com" />
  </div>
  <div class="mb-3 col-md-6">
    <label for="city" class="form-label">Ciudad</label>
    <input type="text" class="form-control" id="city" name="city"
      value="{{ old('city', $rider->city ?? '') }}" />
  </div>
  <div class="mb-3 col-md-6">
    <label for="password" class="form-label">Contraseña</label>
    <input type="password" class="form-control" id="password" name="password"
      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
    @isset($rider)
      <small class="text-muted">Dejar en blanco para no cambiar la contraseña.</small>
    @endisset
  </div>
  <div class="mb-3 col-md-6">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select">
      <option value="active" @selected(old('status', $rider->status ?? '') == 'active')>Activo</option>
      <option value="inactive" @selected(old('status', $rider->status ?? '') == 'inactive')>Inactivo</option>
      <option value="blocked" @selected(old('status', $rider->status ?? '') == 'blocked')>Bloqueado</option>
    </select>
  </div>
</div>
<div class="mt-2">
  <button type="submit" class="btn btn-primary me-2">Guardar Cambios</button>
  <a href="{{ route('admin.riders.index') }}" class="btn btn-label-secondary">Cancelar</a>
</div>
