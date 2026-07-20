<?php 

require_once __DIR__.'../../core/Auth.php';

require_once __DIR__.'../../models/Temporada.php';


class TemporadasController{

    private PDO $db;
    private $temporadaModel;

    public function __construct(PDO $db){
        $this->db = $db;
        $this->temporadaModel = new Temporada($db);
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
            'buscar'    =>  $_GET['buscar'] ?? null
        ];

        //configuracion de  paginacion
        $total = $this->temporadaModel->contar($filtros);
        $pagination = Pagination::paginate($total, 10);

         //configuracion de toolbox
        $toolbox = [
            'create'    =>  'index.php?page=temporadas&action=create',
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
        $tableData = TableService::make($temporadas, 'temporadas');
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

        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        view('pages.temporadas.create.php',[
            'errors'    => $errors,
            'old'       => $old
        ]);

    }

    //FUNCION QUE PROCESA DATOS PARA CREAR UNA NUEVA TEMPORADA
    public function store() {
        // verificamos autenticacion de usuario
       if(!Auth::check()){
            redirectRoute('temporadas');
       }

        $errors = [];

        $temporada     = trim($_POST['temporada'] ?? '');
        $fecha_inicio  = $_POST['fecha_inicio'] ?? '';
        $fecha_fin     = $_POST['fecha_fin'] ?? '';
        $observaciones   = $_POST['observaciones'] ?? null;

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

        //Nombre único
        if ($temporada !== '' && $this->temporadaModel->existeNombre($temporada)) {
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
            'observaciones'   => $observaciones
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
        $temporada = $this->temporadaModel->getById($_GET['id']);   // llamamos al modelo para obtener la temporada con el ID enviado

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

        unset($_SESSION['errors'], $_SESSION['old']);

        view('pages.temporadas.create.php', [
            'temporada' => $temporada,
            'errors'    => $errors,
            'old'       => $old,
            'modo'      => 'edit'
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
        if ($temporada !== '' && $this->temporadaModel->existeNombre($temporada, $id)) {
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
        header('Location: index.php?page=temporadas');
    }

    public function activar($id){
        $temporada = $this->temporadaModel->getById($id);
        if(!$temporada){
            flashError('No existe una temporada con el ID solicitado');
            redirectRoute('temporadas');
        }

        if($temporada['estado'] !== 'borrador'){
            flashError('Solo se permite activar temporadas en estado PLANEADO');
            redirectRoute('temporadas');
        }

        try{
            //primero cerrarmos la temporada activa
            $this->temporadaModel->finalizarTemporadaActiva();

            //activamos la temporada con el id solicitado
            $this->temporadaModel->activar($id);

            flashSuccess('Temporada activada correctamente. La temporada activa anterior pasó a finalizada.');
            redirectRoute('temporadas');
        }catch(Exception $e){
            flashError('Error al activar la temporada.');
            redirectRoute('temporadas');
        }
    }

    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?page=temporadas');
            exit;
        }

        $temporada = $this->temporadaModel->getById($id);

        if (!$temporada) {
            header('Location: index.php?page=temporadas');
            exit;
        }
    
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
            redirect('temporadas');
        }

        flashSuccess('Exito. La temporada paso a estado finalizado.');
        redirectRoute('temporadas');

    }

}