<?php
    declare(strict_types = 1);
    
    class AuthorizationService{
        private PDO $db;

        public function __construct(PDO $pdo)
        {
            $this->db = $pdo;
        }

        /** 
         * verificar que el usuario actual posee un permiso por su rol
         */

        public function hasPermission(string $permission):bool{
            $user = Auth::user();
           
            if(!$user){
                return false;
            }

           $sql = "
                SELECT 1
                FROM tb_rol_permisos rp
                INNER JOIN tb_permisos p
                    ON p.id = rp.permiso_id
                WHERE rp.rol_id = :rol_id
                AND p.clave = :permiso
                AND p.estado = 'activo'
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);

            $stmt ->execute([
                ':rol_id'   => (int) $user['rol_id'],
                ':permiso'  => $permission
            ]);

            return (bool)$stmt->fetchColumn();
        }

        /**
         * verificar si el usuario tiene alcance sobre una entidad concreta
         * Ejemplos:
         * Liga/5 
         * Equipo/18 
         * Organismo/1
         */

        public function hasScope(
            string $tipoEntidad, 
            int $entidadId, 
            ?int $organismoId = null):bool
        {
            $user = Auth::user();
            if(!$user){
                return false;
            }

            /**
             * Admin se considera de alcnace global
             */

            if(($user['rol_clave'] ?? null) === 'ADMIN'){
                return true;
            }

            $sql = "
                SELECT 1
                FROM tb_usuario_alcances ua
                WHERE ua.usuario_id = :usuario_id
                AND ua.tipo_entidad = :tipo_entidad
                AND ua.entidad_id = :entidad_id
                AND ua.estado = 'activo'

                AND (
                    ua.fecha_inicio IS NULL
                    OR ua.fecha_inicio <= :fecha_actual
                )

                AND (
                    ua.fecha_fin IS NULL
                    OR ua.fecha_fin >= :fecha_actual
                )
            ";

            $params = [
                ':usuario_id'  => (int) $user['id'],
                ':tipo_entidad' => $tipoEntidad,
                ':entidad_id'  => $entidadId,
                ':fecha_actual' => date('Y-m-d')
            ];

            /*
            * Si se recibe organismo, el alcance debe pertenecer
            * a ese organismo.
            */
            if ($organismoId !== null) {
                $sql .= " AND ua.organismo_id = :organismo_id";

                $params[':organismo_id'] = $organismoId;
            }

            $sql .= "LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return (bool) $stmt->fetchColumn();

        }

        public function can(
            string $permission,
            ?string $tipoEntidad = null,
            ?int $entidadId = null,
            ?int $organismoId = null
        ): bool 
        {
        
            if (!$this->hasPermission($permission)) {
                return false;
            }

            /*
            * Cuando la operación no depende de una entidad concreta,
            * basta con el permiso.
            */
            if ($tipoEntidad === null || $entidadId === null) {
                return true;
            }

            return $this->hasScope(
                $tipoEntidad,
                $entidadId,
                $organismoId
            );
        }

    }
?>