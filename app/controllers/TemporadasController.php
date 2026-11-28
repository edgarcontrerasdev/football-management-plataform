<?php 

require_once __DIR__ . '/../core/Auth.php';

require_once __DIR__ . '/../models/Temporada.php';
require_once __DIR__ . '/../services/AuthorizationService.php';


class TemporadasController{

    private PDO $db;
    private AuthorizationService $authorization;

    private function idsTemporadasAutorizadas(): ?array
    {
        $user = Auth::user();
        if (!$user) return [];
        if (($user['rol_clave'] ?? '') === 'ADMIN') return null;

        $temporadaIds = $this->authorization->getScopedEntityIds('temporada') ?? [];
        $organismoIds = $this->authorization->getScopedEntityIds('organismo') ?? [];
        $ligaIds = $this->authorization->getScopedEntityIds('liga') ?? [];
        $conditions = [];
        $params = [];

        foreach ($organismoIds as $i => $id) {
            $conditions[] = "(ambito_tipo = 'organismo' AND organismo_id = :org{$i})";
            $params[":org{$i}"] = (int) $id;
        }
        foreach ($ligaIds as $i => $id) {
            $conditions[] = "(ambito_tipo = 'liga' AND liga_propietaria_id = :liga{$i})";
            $params[":liga{$i}"] = (int) $id;
        }

        // También son visibles las temporadas inscritas/vinculadas a las ligas
        // que administra el organismo o a las que el usuario tiene acceso.
        $ligaRelacionadaIds = array_values(array_unique(array_merge(
            array_map('intval', $ligaIds),
            array_map('intval', $this->obtenerLigasGestorasDeOrganismos($organismoIds))
        )));
        if ($ligaRelacionadaIds) {
            $holders = [];
            foreach ($ligaRelacionadaIds as $i => $id) {
                $holders[] = ":liga_relacionada{$i}";
                $params[":liga_relacionada{$i}"] = $id;
            }
            $conditions[] = "id IN (
                SELECT lt.temporada_id
                FROM tb_liga_temporadas lt
                WHERE lt.liga_id IN (" . implode(',', $holders) . ")
            )";
        }

        if ($temporadaIds) {
            $holders = [];
            foreach ($temporadaIds as $i => $id) {
                $holders[] = ":temp{$i}";
                $params[":temp{$i}"] = (int) $id;
            }
            $conditions[] = "id IN (" . implode(',', $holders) . ")";
        }
        if (!$conditions) return [];

        $stmt = $this->db->prepare("SELECT DISTINCT id FROM tb_temporadas WHERE " . implode(' OR ', $conditions));
        $stmt->execute($params);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function obtenerLigasGestorasDeOrganismos(array $organismoIds): array
    {
        if (!$organismoIds) return [];
        $holders = [];
        $params = [];
        foreach (array_values($organismoIds) as $i => $id) {
            $holders[] = ":organismo{$i}";
            $params[":organismo{$i}"] = (int) $id;
        }
        $stmt = $this->db->prepare("
            SELECT DISTINCT liga_id
            FROM tb_liga_organismos
            WHERE organismo_id IN (" . implode(',', $holders) . ")
              AND es_gestor = 1
              AND estado = 'activa'
        ");
        $stmt->execute($params);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function puedeVerTemporada(array $temporada): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if (($user['rol_clave'] ?? '') === 'ADMIN') return true;

        if ($this->authorization->hasScope('temporada', (int) $temporada['id'])) {
            return true;
        }

        $rol = $user['rol_clave'] ?? '';
        if (($temporada['ambito_tipo'] ?? '') === 'organismo' && !empty($temporada['organismo_id'])) {
            return in_array($rol, ['ORGANISMO', 'SUPERVISOR'], true)
                && $this->authorization->hasScope('organismo', (int) $temporada['organismo_id']);
        }
        if (($temporada['ambito_tipo'] ?? '') === 'liga' && !empty($temporada['liga_propietaria_id'])) {
            return in_array($rol, ['LIGA', 'SUPERVISOR'], true)
                && $this->authorization->hasScope('liga', (int) $temporada['liga_propietaria_id']);
        }
        return false;
    }

    private function puedeGestionarTemporada(array $temporada): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $rol = $user['rol_clave'] ?? '';
        // ADMIN global es de consulta: nunca administra el ciclo de vida.
        if ($rol === 'ADMIN') return false;

        // Las temporadas institucionales solo las administra el organismo propietario.
        // Una liga vinculada no adquiere control sobre temporadas del organismo.
        if (($temporada['ambito_tipo'] ?? '') === 'organismo' && !empty($temporada['organismo_id'])) {
            if ($rol === 'ORGANISMO' || $rol === 'SUPERVISOR') {
                return $this->authorization->hasScope('organismo', (int) $temporada['organismo_id'])
                    || ($rol === 'SUPERVISOR' && $this->authorization->hasScope('temporada', (int) $temporada['id']));
            }
            return false;
        }

        if (($temporada['ambito_tipo'] ?? '') === 'liga' && !empty($temporada['liga_propietaria_id'])) {
            if ($rol === 'LIGA' || $rol === 'SUPERVISOR') {
                return $this->authorization->hasScope('liga', (int) $temporada['liga_propietaria_id'])
                    || ($rol === 'SUPERVISOR' && $this->authorization->hasScope('temporada', (int) $temporada['id']));
            }
        }
        return false;
    }

    private function exigirLecturaTemporada(?array $temporada): void
    {
        if (!$temporada || !$this->puedeVerTemporada($temporada)) {
            http_response_code(403);
            flashError('No tienes permisos para consultar esta temporada.');
            redirectRoute('temporadas');
            exit;
        }
    }

    private function exigirGestionTemporada(?array $temporada): void
    {
        if (!$temporada || !$this->puedeGestionarTemporada($temporada)) {
            http_response_code(403);
            flashError('No tienes permisos para administrar esta temporada.');
            redirectRoute('temporadas');
            exit;
        }
    }
    private $temporadaModel;

    public function __construct(PDO $db){
        $this->db = $db;
        $this->temporadaModel = new Temporada($db);
        $this->authorization = new AuthorizationService($db);
    }

    public function index(){
        //verificamos autenticacion
        if(!Auth::check()){
            redirectRoute('temporadas');
        }

        //configuracion de  estados para el select (valor => etiqueta)
        $estados = [
            ''              => 'Todas',
            'cerrada'       => 'Cerradas',
            'activa'        => 'Activas',
            'borrador'      => 'Borrador',
            'finalizada'    => 'Finalizada'
        ];

        //configuracion de filtros
        $filtros = [
            'estado'    =>  $_GET['estado'] ?? null,
            'buscar'    =>  $_GET['buscar'] ?? null,
            'ids'       =>  $this->idsTemporadasAutorizadas()
        ];

        //configuracion de  paginacion
        $total = $this->temporadaModel->contar($filtros);
        $pagination = Pagination::paginate($total, 10);

         //configuracion de toolbox
        $toolbox = [
            'create'    =>  (Auth::user()['rol_clave'] ?? '') === 'ADMIN' ? null : 'index.php?page=temporadas&action=create',
            'filters'   =>  [
                [
                    'type'      =>  'select',
                    'name'      =>  'estado',
                    'label'     =>  null,
                    'options'   =>  $estados,
                    'default'   =>  1
                ],
                [
                    'type'          => 'search',
                    'name'          => 'buscar',
                    'placeholder'   => 'buscar temporada'
                ]
            ]

        ];

        //consulta de datos al modelo
        $temporadas = $this->temporadaModel->listar($filtros, $pagination['limit'], $pagination['offset']);
       
        //tabla final
        $tableData = TableService::make($temporadas, 'temporadas', [
            'read_only' => (Auth::user()['rol_clave'] ?? '') === 'ADMIN'
        ]);
        $columns = $tableData['columns'];
        $rows = $tableData['rows'];

        //comprobar si se mando algo por AJAX y se muestra
        if(isset($_GET['ajax'])){
            include ROOT_PATH.'/admin/views/components/table-content.php';
            return;
        }   

        //formarmos y cargamos la vista
        view('pages.temporadas.index.php',[
            'temporadas'        =>  $temporadas,
            'columns'           =>  $columns,
            'rows'              =>  $rows,
            'toolbox'           =>  $toolbox,
            'pagination'        =>  $pagination
        ]);

    }

    //FUNCION QUE MUESTRA LA VISTA PARA CREAR NUEVA TEMPORADA
    public function create(){
       // verificamos autenticacion de usuario
       if(!Auth::check()){
            redirectRoute('temporadas');
       }

        // Las temporadas pertenecen a un organismo o a una liga independiente.
        // El ADMIN global no puede crear ni asignar temporadas a otros ámbitos.
        if ((Auth::user()['rol_clave'] ?? '') === 'ADMIN') {
            http_response_code(403);
            flashError('Las temporadas deben ser creadas por el organismo o la liga independiente que las administra.');
            redirectRoute('temporadas');
            exit;
        }

        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old'] ?? [];
        $propietarios = $this->temporadaModel->listarPropietarios();
        $user = Auth::user();
        if (($user['rol_clave'] ?? '') !== 'ADMIN') {
            $orgIds = $this->authorization->getScopedEntityIds('organismo') ?? [];
            $ligaIds = $this->authorization->getScopedEntityIds('liga') ?? [];
            $propietarios['organismos'] = array_values(array_filter($propietarios['organismos'], fn($item) => in_array((int) $item['id'], $orgIds, true)));
            $propietarios['ligas'] = array_values(array_filter($propietarios['ligas'], fn($item) => in_array((int) $item['id'], $ligaIds, true)));
        }
        unset($_SESSION['errors'], $_SESSION['old']);

        view('pages.temporadas.create.php',[
            'errors'    => $errors,
            'old'       => $old,
            'propietarios' => $propietarios
        ]);

    }

    //FUNCION QUE PROCESA DATOS PARA CREAR UNA NUEVA TEMPORADA
    public function store() {
        // verificamos autenticacion de usuario
       if(!Auth::check()){
            redirectRoute('temporadas');
       }

        // Bloqueo servidor-side: ADMIN global no puede asignar temporadas a organismos o ligas.
        if ((Auth::user()['rol_clave'] ?? '') === 'ADMIN') {
            http_response_code(403);
            flashError('No tienes permisos para crear temporadas. Cada organismo o liga independiente administra las propias.');
            redirectRoute('temporadas');
            exit;
        }

        $errors = [];

        $temporada     = trim($_POST['temporada'] ?? '');
        $fecha_inicio  = $_POST['fecha_inicio'] ?? '';
        $fecha_fin     = $_POST['fecha_fin'] ?? '';
        $observaciones   = $_POST['observaciones'] ?? null;
        $propietario = trim((string) ($_POST['propietario'] ?? ''));
        [$ambitoTipo, $propietarioValor] = array_pad(explode(':', $propietario, 2), 2, '');
        $propietarioId = (int) $propietarioValor;

        // Campos obligatorios
        if ($temporada === '') {
            $errors[] = 'El nombre de la temporada es obligatorio';
        }

        if ($fecha_inicio === '' || $fecha_fin === '') {
            $errors[] = 'Las fechas de inicio y fin son obligatorias';
        }

        //Validar formato de fechas
        if ($fecha_inicio && !strtotime($fecha_inicio)) {
            $errors[] = 'La fecha de inicio no es válida';
        }

        if ($fecha_fin && !strtotime($fecha_fin)) {
            $errors[] = 'La fecha de fin no es válida';
        }

        //Orden lógico de fechas
        if ($fecha_inicio && $fecha_fin) {
            if (strtotime($fecha_inicio) >= strtotime($fecha_fin)) {
                $errors[] = 'La fecha de inicio debe ser menor a la fecha de fin';
            }
        }

        // Validar propietario y nombre dentro del ámbito.
        if (!in_array($ambitoTipo, ['organismo', 'liga'], true) || $propietarioId <= 0) {
            $errors[] = 'Selecciona el organismo o liga propietaria de la temporada';
        }
        if ($propietarioId > 0 && $ambitoTipo === 'organismo' && !$this->authorization->hasScope('organismo', $propietarioId)) {
            $errors[] = 'No tienes permisos sobre el organismo seleccionado';
        }
        if ($propietarioId > 0 && $ambitoTipo === 'liga' && !$this->authorization->hasScope('liga', $propietarioId)) {
            $errors[] = 'No tienes permisos sobre la liga seleccionada';
        }

        if ($temporada !== '' && $propietarioId > 0 && $this->temporadaModel->existeNombre($temporada, null, $ambitoTipo, $propietarioId)) {
            $errors[] = 'Ya existe una temporada con ese nombre';
        }
  
        // Si hay errores, regresar al formulario
        if (!empty($errors)) {
            flashError($errors);
            $_SESSION['old'] = $_POST;
            redirectRoute('temporadas',[
                'action'    => 'create'
            ]);
        }

        //si no hay errores llamamos la funcion crear del modelo 
        $result = $this->temporadaModel->crear([
            'temporada'     => $temporada,
            'fecha_inicio'  => $fecha_inicio,
            'fecha_fin'     => $fecha_fin,
            'estado'        => 'borrador',
            'observaciones'   => $observaciones,
            'ambito_tipo' => $ambitoTipo,
            'organismo_id' => $ambitoTipo === 'organismo' ? $propietarioId : null,
            'liga_propietaria_id' => $ambitoTipo === 'liga' ? $propietarioId : null
        ]);

        //si ocurrio un error al intentar guardar 
        if(!$result){
            flashError('No se pudo crear la temporada. Intente de nuevo');  // enviamos mensaje de error
            $_SESSION['old'] = $_POST;                     // guardamos datos de sesion
            redirectRoute('temporadas');                   // redirigimos a temporadas -> index
        }

        //si no hubo error 
        flashSuccess('Temporada creada con exito');      // enviamos mensaje de exito
        redirectRoute('temporadas');                    // rederijimos a temporadas -> index
    }

    //FUNCION QUE MUESTRA LA VISTA DE EDICION DE UNA TEMPORADA
    public function edit()
    {
        // verificamos autenticacion de usuario
        if(!Auth::check()){
            flashError('Acceso invalido. Sesion no iniciada.');
            redirectRoute('temporadas');        //redirigimos a temporas->index
        }

        //verificamos que no venga vacio el ID 
        if (empty($_GET['id'])) {
            flashError('No se recibio el ID de la temporada');
            redirectRoute('temporadas');        //rederigimos a temporadas->index
        }

        // si el ID no viene vacio 
        $temporada = $this->temporadaModel->getById($_GET['id']);
        $this->exigirGestionTemporada($temporada);   // llamamos al modelo para obtener la temporada con el ID enviado

        // si no existe la temporada
        if (!$temporada) {
            flashError('El ID de la temporada no es valido.');
            redirectRoute('temporadas');
        }

        if ($temporada['estado'] === 'cerrada') {
            flashError('No se puede editar una temporada cerrada.');
            redirectRoute('temporadas');
        }

        if ($temporada['estado'] === 'activa') {
            flashError('No se puede editar una temporada activa.');
            redirectRoute('temporadas');
        }

        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old'] ?? [];
        $propietarios = $this->temporadaModel->listarPropietarios();

        unset($_SESSION['errors'], $_SESSION['old']);

        view('pages.temporadas.create.php', [
            'temporada' => $temporada,
            'errors'    => $errors,
            'old'       => $old,
            'modo'      => 'edit',
            'propietarios' => $propietarios
        ]);
    }

    //FUNCION QUE PROCESA DATOS PARA ACTUALIZAR LA TEMPORADA
    public function update() {

        if (empty($_POST['id'])) {
            flashError('No se recibio el ID de la temporada');
            redirectRoute('temporadas');
        }

        $errors = []; 
        $id = $_POST['id'];

        $temporadaActual = $this->temporadaModel->getById($id);
        $this->exigirGestionTemporada($temporadaActual);

        if (!$temporadaActual) {
            flashError('No existe la temporada solicitada.');
            redirectRoute('temporadas');
        }

        if (in_array($temporadaActual['estado'], ['finalizada', 'cerrada'])) {
            flashError('No se puede actualizar una temporada finalizada o cerrada.');
            redirectRoute('temporadas');
        }

        $temporada     = trim($_POST['temporada'] ?? '');
        $fecha_inicio  = $_POST['fecha_inicio'] ?? '';
        $fecha_fin     = $_POST['fecha_fin'] ?? '';
        $observaciones   = $_POST['observaciones'] ?? null;

        if ($temporada === '') {
            $errors[] = 'El nombre de la temporada es obligatorio';
        }

        if ($fecha_inicio === '' || $fecha_fin === '') {
            $errors[] = 'Las fechas de inicio y fin son obligatorias';
        }

        if ($fecha_inicio && $fecha_fin && strtotime($fecha_inicio) >= strtotime($fecha_fin)) {
            $errors[] = 'La fecha de inicio debe ser menor a la fecha de fin';
        }

        //Nombre único
        if ($temporada !== '' && $this->temporadaModel->existeNombre(
            $temporada, $id, $temporadaActual['ambito_tipo'],
            (int) ($temporadaActual['organismo_id'] ?: $temporadaActual['liga_propietaria_id'])
        )) {
            $errors[] = 'Ya existe una temporada con ese nombre';
        }

        //si existen errorrs
        if (!empty($errors)) {
            flashError($errors);
            $_SESSION['old'] = $_POST;
            redirectRoute('temporadas',[
                'action'    => 'updated',
                'id'        => $id
            ]);
        }

        $result = $this->temporadaModel->actualizar($id, [
            'temporada'     => $temporada,
            'fecha_inicio'  => $fecha_inicio,
            'fecha_fin'     => $fecha_fin,
            'observaciones'   => $observaciones,
            'estado'        => $temporadaActual['estado'] // 🔒 no se cambia
        ]);

        if(!$result){
            flashError('Ocurrio un error al intentar actualizar la temporada');
            redirectRoute('temporadas',[
                'action'        => 'updated',
                'id'            => $id
            ]);
        }

        flashSuccess('La temporada fue actualizada con exito.');
        redirectRoute('temporadas');
    }

    public function activar($id){
        $temporada = $this->temporadaModel->getById($id);
        $this->exigirGestionTemporada($temporada);
        if(!$temporada){
            flashError('No existe una temporada con el ID solicitado');
            redirectRoute('temporadas');
        }

        if (!in_array($temporada['estado'], ['borrador', 'planeada'], true)) {
            flashError('Solo se pueden activar temporadas en estado planeada o borrador.');
            redirectRoute('temporadas');
        }

        try{
            // La activación solo finaliza la temporada anterior del mismo propietario.
            $this->temporadaModel->activar($id);

            flashSuccess('Temporada activada correctamente. La temporada activa anterior pasó a finalizada.');
            redirectRoute('temporadas');
        }catch(Exception $e){
            flashError('Error al activar la temporada.');
            redirectRoute('temporadas');
        }
    }

    /**
     * El organismo propietario resuelve la participación de una liga en su temporada.
     */
    public function resolverParticipacionLiga()
    {
        if (!Auth::check() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            flashError('Solicitud inválida.');
            redirectRoute('temporadas');
        }

        $temporadaId = (int) ($_POST['temporada_id'] ?? 0);
        $ligaId = (int) ($_POST['liga_id'] ?? 0);
        $decision = (string) ($_POST['decision'] ?? '');
        $estado = $decision === 'aprobar'
            ? 'activo'
            : ($decision === 'rechazar' ? 'rechazado' : ($decision === 'retirar' ? 'revocado' : ''));

        $temporada = $this->temporadaModel->getById($temporadaId);
        $user = Auth::user();

        // Esta decisión corresponde exclusivamente al administrador del organismo propietario.
        if (
            !$temporada
            || ($temporada['ambito_tipo'] ?? '') !== 'organismo'
            || ($user['rol_clave'] ?? '') !== 'ORGANISMO'
            || !$this->authorization->hasScope('organismo', (int) $temporada['organismo_id'])
        ) {
            http_response_code(403);
            flashError('Solo el organismo propietario puede autorizar ligas en esta temporada.');
            redirectRoute('temporadas');
        }

        if ($ligaId <= 0 || $estado === '') {
            flashError('La liga o decisión seleccionada no es válida.');
            redirectRoute('temporadas', ['action' => 'show', 'id' => $temporadaId]);
        }

        try {
            $resultado = $this->temporadaModel->actualizarParticipacionLiga(
                $temporadaId,
                $ligaId,
                $estado,
                (int) Auth::id()
            );
            if (!$resultado) {
                flashError('La liga no pertenece a este organismo o ya no está disponible.');
            } else {
                flashSuccess($estado === 'activo'
                    ? 'La liga quedó autorizada para participar en esta temporada.'
                    : ($estado === 'revocado'
                        ? 'La autorización fue retirada. El historial de equipos y participaciones se conservó.'
                        : 'La participación de la liga fue rechazada.'));
            }
        } catch (Throwable $e) {
            flashError('No fue posible actualizar la participación de la liga.');
        }

        redirectRoute('temporadas', ['action' => 'show', 'id' => $temporadaId]);
    }

    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?page=temporadas');
            exit;
        }

        $temporada = $this->temporadaModel->getById($id);
        $this->exigirLecturaTemporada($temporada);

        if (!$temporada) {
            header('Location: index.php?page=temporadas');
            exit;
        }
    
        $esAdminGlobal = (Auth::user()['rol_clave'] ?? '') === 'ADMIN';
        $puedeGestionarTemporada = $this->puedeGestionarTemporada($temporada);
        $puedeGestionarParticipantes = ($temporada['ambito_tipo'] ?? '') === 'organismo'
            && (Auth::user()['rol_clave'] ?? '') === 'ORGANISMO'
            && $this->authorization->hasScope('organismo', (int) $temporada['organismo_id']);
        $ligasParticipantes = ($temporada['ambito_tipo'] ?? '') === 'organismo'
            ? $this->temporadaModel->listarLigasParticipantes((int) $temporada['id'])
            : [];

        //ruta de la vista show.php
        $viewPath = ROOT_PATH."/admin/views/pages/temporadas/show.php";

        //si no existe la vista cargamos la vista de error 404
        if(!file_exists($viewPath)){
            $viewPath = ROOT_PATH.'/admin/views/404.php';
        }

        //incluimos el layout
        //el layout imprime la variable $viewPath
        //que es la que contiene la vista
        include ROOT_PATH.'/admin/views/layout.php';

    }

    public function delete($id){
        // verificamos autenticacion de usuario
        if(!Auth::check()){
            flashError('Acceso invalido. Sesion no iniciada.');
            redirectRoute('temporadas');        //redirigimos a temporas->index
        }

        //verificamos que no venga vacio el ID 
        if (empty($_GET['id'])) {
            flashError('No se recibio el ID de la temporada');
            redirectRoute('temporadas');        //rederigimos a temporadas->index
        }

        //obtenemos el id de la temporada y la buscamos en la base de datos
        $id = $_GET['id'];
        $temporada = $this->temporadaModel->getById($id);
        $this->exigirGestionTemporada($temporada);

        //si no se obtuvo resultado en la consulta
        if(!$temporada){
            flashError('No se encontro la temporada solicitada');
            redirectRoute('temporadas');
        }

        //comprobamos que la temporada que se va a cerrar este previamente finalizada
        if($temporada['estado'] !== 'borrador'){
            flashError('ERROR, solo pueden cerrarse temporadas en estado planeado.');
            redirectRoute('temporadas');
        }

        $result = $this->temporadaModel->eliminar($id);

        //si ocurrio un error en la consulta a la BD
        if(!$result){
            flashError('Ocurrio un error al intentar eliminar la temporada.');
            redirectRoute('temporadas');
        }

        flashSuccess('Temporada eliminada con exito.');
        redirectRoute('temporadas');

    }

    public function cerrar()
    {
        if (!Auth::check()) {
            flashError('Acceso inválido. No has iniciado sesión.');
            redirectRoute('temporadas');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            flashError('Solicitud inválida.');
            redirectRoute('temporadas');
        }

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            flashError('No se recibió el ID de la temporada.');
            redirectRoute('temporadas');
        }

        $temporada = $this->temporadaModel->getById($id);
        $this->exigirGestionTemporada($temporada);

        if (!$temporada) {
            flashError('ERROR. El ID recibido no es válido o no existe.');
            redirectRoute('temporadas');
        }

        if (!in_array($temporada['estado'],['activa','finalizada'])) {
            flashError('Solo se puede cerrar una temporada activa o finalizada.');
            redirectRoute('temporadas');
        }

        $result = $this->temporadaModel->cerrar($id);

        if (!$result) {
            flashError('Ocurrió un error al intentar cerrar la temporada.');
            redirectRoute('temporadas');
        }

        flashSuccess('La temporada fue cerrada con éxito.');
        redirectRoute('temporadas');
    }

    public function finalizar()
    {
        if(!Auth::check()){
            flashError('Acceso inválido. No has iniciado sesión.');            
            redirectRoute('temporadas');
        }

        if($_SERVER['REQUEST_METHOD'] !== 'GET'){
            flashError('Solicitud invalida.');
            redirectRoute('temporadas');
        }

        $id = $_GET['id'];
        $temporada = $this->temporadaModel->getById($id);
        $this->exigirGestionTemporada($temporada);

        if(!$temporada){
            flashError('ERROR. El ID recibido no es valido o no existe');
            redirectRoute('temporadas');
        }

        if($temporada['estado'] !== 'activa'){
            flashError('Solo se permite finalizar temporadas en estado Activa.');
            redirectRoute('temporadas');
        }

        $result = $this->temporadaModel->finalizarTemporada($id);

        if(!$result){
            flashError('Ocurrio un error al finalizar la temporada');
            redirectRoute('temporadas');
        }

        flashSuccess('Exito. La temporada paso a estado finalizado.');
        redirectRoute('temporadas');

    }

}