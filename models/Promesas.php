<?php
namespace Model;

use Model\ActiveRecord;

class Promesas extends ActiveRecord
{

    // Resumen de promesas por gestor
    public static function obtenerResumen()
    {
        self::useSQLSrv();
        $sql = "
        SELECT 
            creado_por AS Gestor,
            SUM(CASE WHEN estado_promesa = 'CUMPLIDA' THEN 1 ELSE 0 END) AS Cumplidas,
            SUM(CASE WHEN estado_promesa = 'INCUMPLIDA' THEN 1 ELSE 0 END) AS Incumplidas,
            CASE 
                WHEN SUM(CASE WHEN estado_promesa IN ('CUMPLIDA','INCUMPLIDA') THEN 1 ELSE 0 END) = 0 
                THEN 0
                ELSE ROUND(
                    100.0 * SUM(CASE WHEN estado_promesa = 'CUMPLIDA' THEN 1 ELSE 0 END) /
                    NULLIF(SUM(CASE WHEN estado_promesa IN ('CUMPLIDA','INCUMPLIDA') THEN 1 ELSE 0 END),0)
                ,2)
            END AS PorcentajeCumplimiento
        FROM vw_PromesasDetalle
        WHERE MONTH(fecha_creacion) = MONTH(GETDATE())
          AND YEAR(fecha_creacion) = YEAR(GETDATE())
        GROUP BY creado_por
        ORDER BY PorcentajeCumplimiento DESC;
    ";

        return self::consultarSQL($sql);
    }

    // Detalle por gestor (solo promesas del mes actual)
    public static function obtenerDetallePorGestor($gestor)
    {
        self::useSQLSrv();
        $sql = "
        SELECT 
            prenumero,
            fecha_creacion,
            fecha_promesa,
            montoPromesa,
            TotalPagado,
            estado_promesa
        FROM vw_PromesasDetalle
        WHERE creado_por = :gestor
          AND MONTH(fecha_creacion) = MONTH(GETDATE())
          AND YEAR(fecha_creacion) = YEAR(GETDATE())
        ORDER BY fecha_creacion DESC
    ";

        return self::consultarSQL($sql, [':gestor' => $gestor]);
    }

}
