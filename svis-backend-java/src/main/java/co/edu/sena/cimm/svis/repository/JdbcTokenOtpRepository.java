package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Token_OTP;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.time.LocalDateTime;
import java.util.Optional;

public class JdbcTokenOtpRepository implements TokenOtpRepository {

    private static final String Columnas = "id, encuesta_id, usuario_id, codigo, estado, expira_en";

    @Override
    public void generarYGuardar(Long idEncuesta, Long idUsuario, String codigo, LocalDateTime expiraEn) {
        String sql = "INSERT INTO token_otp(encuesta_id, usuario_id, codigo, estado, expira_en) VALUES (?, ?, ?, 'DISPONIBLE', ?)";
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setLong(1, idEncuesta);
            ps.setLong(2, idUsuario);
            ps.setString(3, codigo);
            if (expiraEn == null) ps.setNull(4, java.sql.Types.TIMESTAMP);
            else ps.setTimestamp(4, Timestamp.valueOf(expiraEn));
            ps.executeUpdate();
        } catch (SQLException e) {
            throw new RuntimeException("Error generando token OTP (¿duplicado encuesta+usuario o encuesta+codigo?)", e);
        }
    }

    @Override
    public boolean usuarioYaVoto(Long idUsuario, Long idEncuesta) {
        String sql = "select 1 from token_otp where usuario_id = ? and encuesta_id = ? and estado = 'USADO' limit 1";
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setLong(1, idUsuario);
            ps.setLong(2, idEncuesta);
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next();
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error verificando voto previo", e);
        }
    }

    @Override
    public boolean estaExpirado(String codigo) {
        String sql = "select expira_en from token_otp where codigo = ?";
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, codigo);
            try (ResultSet rs = ps.executeQuery()) {
                if (!rs.next()) return true;
                Timestamp ts = rs.getTimestamp("expira_en");
                if (ts == null) return false;
                return ts.toLocalDateTime().isBefore(LocalDateTime.now());
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error verificando expiración OTP", e);
        }
    }

    @Override
    public String validarYAplicarRegla(String codigo, Long idUsuario, Long idEncuesta) {
        Optional<Token_OTP> opt = buscarPorCodigo(codigo, idUsuario, idEncuesta);
        if (!opt.isPresent()) return "NO_EXISTE";
        Token_OTP t = opt.get();
        if ("USADO".equalsIgnoreCase(t.getEstado())) return "YA_USADO";
        if (t.getExpira_en() != null && t.getExpira_en().isBefore(LocalDateTime.now())) return "EXPIRADO";
        return "OK";
    }

    @Override
    public Optional<Token_OTP> buscarPorCodigo(String codigo, Long idUsuario, Long idEncuesta) {
        String sql = "select " + Columnas + " from token_otp where codigo = ? and usuario_id = ? and encuesta_id = ?";
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, codigo);
            ps.setLong(2, idUsuario);
            ps.setLong(3, idEncuesta);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) return Optional.of(map(rs));
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error buscando token OTP", e);
        }
        return Optional.empty();
    }

    @Override
    public Optional<Token_OTP> buscarPorCodigoForUpdate(Connection c, String codigo, Long idUsuario, Long idEncuesta) throws SQLException {
        String sql = "select " + Columnas + " from token_otp where codigo = ? and usuario_id = ? and encuesta_id = ? FOR UPDATE";
        try (PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, codigo);
            ps.setLong(2, idUsuario);
            ps.setLong(3, idEncuesta);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) return Optional.of(map(rs));
            }
        }
        return Optional.empty();
    }

    @Override
    public void marcarUsado(Connection c, String codigo) throws SQLException {
        String sql = "UPDATE token_otp SET estado = 'USADO' WHERE codigo = ?";
        try (PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, codigo);
            ps.executeUpdate();
        }
    }

    @Override
    public java.util.List<Token_OTP> listarPorEncuesta(Long encuestaId) {
        String sql = "select " + Columnas + " from token_otp where encuesta_id = ? order by id";
        java.util.List<Token_OTP> lista = new java.util.ArrayList<>();
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setLong(1, encuestaId);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) lista.add(map(rs));
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error listando tokens", e);
        }
        return lista;
    }

    private Token_OTP map(ResultSet rs) throws SQLException {
        Token_OTP t = new Token_OTP();
        t.setId(rs.getLong("id"));
        t.setEncuesta_id(rs.getLong("encuesta_id"));
        t.setUsuario_id(rs.getLong("usuario_id"));
        t.setCodigo(rs.getString("codigo"));
        t.setEstado(rs.getString("estado"));
        Timestamp ts = rs.getTimestamp("expira_en");
        t.setExpira_en(ts == null ? null : ts.toLocalDateTime());
        return t;
    }
}
