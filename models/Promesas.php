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
            SUM(CASE WHEN estado_promesa = 'INCUMPLIDA' THEN 1 ELSE 0 END) AS Incumplidas,+
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
        $sql = "SELECT 
            d.prenumero,
            fecha_creacion,
            fecha_promesa,
            montoPromesa,
            TotalPagado,
            estado_promesa,
            creado_por as nombregestor,
            serie.CrCalVAlfa AS serie
        FROM vw_PromesasDetalle as d
        OUTER APPLY (
                SELECT TOP 1 s.CrCalVAlfa
                FROM  [192.168.1.60].[SKG_BP].[SIFCO].[CrCalSPLevel1] s
                WHERE s.CrCalNumero = d.PreNumero
                  AND s.ApCalCod = 16
                ORDER BY s.CrCalCorre DESC   -- 🔹 última serie registrada
            ) serie
        WHERE creado_por = :gestor
          AND MONTH(fecha_creacion) = MONTH(GETDATE())
          AND YEAR(fecha_creacion) = YEAR(GETDATE())
        ORDER BY fecha_creacion DESC
    ";

        return self::consultarSQL($sql, [':gestor' => $gestor]);
    }
    public static function obtenerDetallePorEstado($estado)
    {
        self::useSQLSrv();
        $sql = "SELECT distinct
            d.prenumero,
            fecha_creacion,
            fecha_promesa,
            montoPromesa,
            TotalPagado,
            estado_promesa,
            creado_por AS Gestor,
            nombregestor,
			serie.CrCalVAlfa AS serie
        FROM vw_PromesasDetalle as d
        left join TBL_USER_COBROS as u on d.creado_por = u.nombre
        left join prestamosGestor as pg on u.usuario = pg.usuarioCobros
		OUTER APPLY (
                SELECT TOP 1 s.CrCalVAlfa
                FROM  [192.168.1.60].[SKG_BP].[SIFCO].[CrCalSPLevel1] s
                WHERE s.CrCalNumero = d.PreNumero
                  AND s.ApCalCod = 16
                ORDER BY s.CrCalCorre DESC   -- 🔹 última serie registrada
            ) serie
        WHERE estado_promesa = :estado
          AND MONTH(fecha_creacion) = MONTH(GETDATE())
          AND YEAR(fecha_creacion) = YEAR(GETDATE())
        ORDER BY fecha_creacion DESC
    ";

        return self::consultarSQL($sql, [':estado' => $estado]);
    }
    public static function descargarPromesas()
    {

        self::useSQLSrv();
        $sql = "SELECT * FROM vw_PromesasDetalle where MONTH(fecha_creacion) = MONTH(GETDATE())
        AND YEAR(fecha_creacion) = YEAR(GETDATE())";
        return self::consultarSQL($sql, null);
    }




}
