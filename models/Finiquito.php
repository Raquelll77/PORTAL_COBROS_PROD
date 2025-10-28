<?php
namespace Model;

use Model\ActiveRecord;

class Finiquito extends ActiveRecord
{
    public static function obtenerPorSerie($serie)
    {
        self::useSQLSrv3(); // Conexión MOVESA

        $sql = "
                SELECT TOP 1
                    t0.CardCode,
                    t0.CardName,
                    (SELECT CardFName FROM OCRD WITH (NOLOCK)
                        WHERE CardCode = t0.CardCode) AS Identidad,   -- 🔹 Identidad del cliente (DNI)
                    t0.DocNum AS NumeroFactura,
                    t16.MnfSerial AS Serie,
                    t16.DistNumber AS Motor,
                    t3.Name AS Marca,
                    t4.Name AS Modelo,
                    t9.Name AS Color,
                    t16.LotNumber AS Año,
                    t10.Name AS Cilindraje
                FROM OINV t0
                    INNER JOIN INV1 t1 WITH (NOLOCK) ON t0.DocEntry = t1.DocEntry
                    INNER JOIN OITM t2 WITH (NOLOCK) ON t2.ItemCode = t1.ItemCode
                    LEFT JOIN [@AMARCA] t3 WITH (NOLOCK) ON t2.U_AMARCA = t3.Code
                    LEFT JOIN [@AMODELO] t4 WITH (NOLOCK) ON t2.U_AMODELO = t4.Code
                    LEFT JOIN [@SCOLOR] t9 WITH (NOLOCK) ON t9.Code = t2.U_ACOLOR
                    LEFT JOIN [@ACILINDROS] t10 WITH (NOLOCK) ON t10.Code = t2.U_ACILINDROS
                    INNER JOIN OSRN t16 WITH (NOLOCK)
                        ON t16.MnfSerial = t1.U_MSerie 
                        AND t16.ItemCode = t1.ItemCode
                WHERE t16.MnfSerial = ?
                ORDER BY t0.DocDate DESC;
                 ";

        $params = [$serie]; // Parámetro posicional

        // tercer parámetro en true (si tu método lo requiere para consultas directas)
        $resultado = self::consultarSQL($sql, $params);

        return $resultado[0] ?? null;
    }
}
