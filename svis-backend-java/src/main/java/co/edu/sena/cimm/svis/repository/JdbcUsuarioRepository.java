package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Usuario;
import java.sql.*;

public class JdbcUsuarioRepository implements UsuarioRepository {

    private static final String Columnas = "u.id, u.username, u.contrasenia, u.nombre, u.documento, u.rol, u.ficha_id";

    @Override
    public Usuario loginAdmin(String username, String contrasenia) {
        String sql = "select " + Columnas + " from usuario u where u.username = ? and u.contrasenia = ? and u.rol = 'ADMIN'";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, username);
            ps.setString(2, contrasenia);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error login admin", e);
        }
        throw new RuntimeException("Credenciales admin inválidas");
    }

    @Override
    public Usuario loginVotante(String documento, String numeroFicha) {
        String sql = "select " + Columnas + " from usuario u JOIN ficha f ON f.id = u.ficha_id "
                + "where u.documento = ? and f.numero = ? and u.rol = 'USUARIO'";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, documento);
            ps.setString(2, numeroFicha);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error login votante", e);
        }
        throw new RuntimeException("No estás en el patrón de esa ficha");
    }

    @Override
    public boolean existeUsername(String username) {
        String sql = "select 1 from usuario where username = ?";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, username);
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next();
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error verificando username", e);
        }
    }

    @Override
    public void registrarUsuario(String nombre, String documento, String username, String contrasenia, String rol, Long fichaId) {
        String sql = "INSERT INTO usuario(username, contrasenia, nombre, documento, rol, ficha_id) VALUES (?, ?, ?, ?, ?, ?)";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, username);
            ps.setString(2, contrasenia);
            ps.setString(3, nombre);
            ps.setString(4, documento);
            ps.setString(5, rol);
            if (fichaId == null) {
                ps.setNull(6, Types.BIGINT);
            } else {
                ps.setLong(6, fichaId);
            }
            ps.executeUpdate();
        } catch (SQLException e) {
            throw new RuntimeException("Error registrando usuario", e);
        }
    }

    private Usuario map(ResultSet rs) throws SQLException {
        Long fid = rs.getObject("ficha_id") == null ? null : rs.getLong("ficha_id");
        return new Usuario(
                rs.getLong("id"),
                rs.getString("username"),
                rs.getString("contrasenia"),
                rs.getString("nombre"),
                rs.getString("documento"),
                rs.getString("rol"),
                fid
        );
    }
}
