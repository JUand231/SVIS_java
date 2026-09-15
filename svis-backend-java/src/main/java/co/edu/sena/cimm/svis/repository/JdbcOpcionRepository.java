package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Opcion;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

public class JdbcOpcionRepository implements OpcionRepository {

    private static final String Columnas = "id, encuesta_id, candidato_id, texto, foto_url, votos_total, jornada";

    @Override
    public List<Opcion> listarOpcionesPorEncuesta(Long idEncuesta) {
        String consulta = "select " + Columnas + " from opcion where encuesta_id = ?";
        List<Opcion> lista = new ArrayList<>();

        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta)) {

            ps.setLong(1, idEncuesta);

            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    lista.add(map(rs));
                }
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error consultando opciones", e);
        }
        return lista;
    }

    @Override
    public void crearOpcion(Long idEncuesta, Long candidatoId, String texto, String fotoUrl, String jornada) {
        String consulta = "insert into opcion(encuesta_id, candidato_id, texto, foto_url, jornada) VALUES (?, ?, ?, ?, ?)";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta, Statement.RETURN_GENERATED_KEYS)) {

            ps.setLong(1, idEncuesta);
            if (candidatoId == null) ps.setNull(2, java.sql.Types.BIGINT);
            else ps.setLong(2, candidatoId);
            ps.setString(3, texto);
            ps.setString(4, fotoUrl);
            ps.setString(5, jornada);
            ps.executeUpdate();

        } catch (SQLException ex) {
            throw new RuntimeException("Error creando la opcion", ex);
        }
    }

    @Override
    public int obtenerContadorVotos(Long idOpcion) {
        String consulta = "select votos_total from opcion where id = ?";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta)) {

            ps.setLong(1, idOpcion);

            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return rs.getInt("votos_total");
                }
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error obteniendo cantidad de votos", e);
        }
        return 0;
    }

    @Override
    public void sumarVoto(Long idOpcion) {
        String consulta = "UPDATE opcion SET votos_total = votos_total + 1 WHERE id = ?";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta)) {

            ps.setLong(1, idOpcion);
            ps.executeUpdate();

        } catch (SQLException e) {
            throw new RuntimeException("Error sumando voto", e);
        }
    }

    private Opcion map(ResultSet rs) throws SQLException {
        Long candId = rs.getObject("candidato_id") == null ? null : rs.getLong("candidato_id");
        return new Opcion(
                rs.getLong("id"),
                rs.getLong("encuesta_id"),
                rs.getString("texto"),
                rs.getInt("votos_total"),
                candId,
                rs.getString("foto_url"),
                rs.getString("jornada"));
    }
}
