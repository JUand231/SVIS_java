package co.edu.sena.cimm.svis.service;

import co.edu.sena.cimm.svis.config.AppContext;
import co.edu.sena.cimm.svis.dto.VotoResponse;
import co.edu.sena.cimm.svis.model.Encuesta;
import co.edu.sena.cimm.svis.model.Token_OTP;
import co.edu.sena.cimm.svis.repository.BaseDeDatos;
import co.edu.sena.cimm.svis.repository.EncuestaRepository;
import co.edu.sena.cimm.svis.repository.TokenOtpRepository;
import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.util.Optional;

public class VotoService {

    private final TokenOtpRepository tokens = AppContext.get().getTokenOtpRepository();
    private final EncuestaRepository encuestas = AppContext.get().getEncuestaRepository();

    public VotoResponse emitirVoto(String codigo, Long opcionId, Long encuestaId, Long usuarioId) {
        VotoResponse r = new VotoResponse();
        if (codigo == null || codigo.trim().isEmpty()) {
            throw new RuntimeException("Codigo requerido");
        }
        if (opcionId == null || encuestaId == null || usuarioId == null) {
            throw new RuntimeException("Datos incompletos");
        }

        codigo = codigo.trim();

        try (Connection c = BaseDeDatos.getConnection()) {
            c.setAutoCommit(false);
            try {
                Optional<Token_OTP> opt = tokens.buscarPorCodigoForUpdate(c, codigo, usuarioId, encuestaId);
                if (!opt.isPresent()) {
                    c.rollback();
                    r.exito = false;
                    r.mensaje = "Token no existe";
                    return r;
                }
                Token_OTP t = opt.get();
                if ("USADO".equalsIgnoreCase(t.getEstado())) {
                    c.rollback();
                    r.exito = false;
                    r.mensaje = "Ya votaste (409)";
                    return r;
                }
                if (t.getExpira_en() != null && t.getExpira_en().isBefore(java.time.LocalDateTime.now())) {
                    c.rollback();
                    r.exito = false;
                    r.mensaje = "Token expirado";
                    return r;
                }

                Encuesta enc = encuestas.obtenerEncuesta(encuestaId);
                if (!enc.getEncuesta().name().equals("ACTIVA")) {
                    c.rollback();
                    r.exito = false;
                    r.mensaje = "Encuesta cerrada";
                    return r;
                }

                tokens.marcarUsado(c, codigo);

                try (PreparedStatement ps = c.prepareStatement("UPDATE opcion SET votos_total = votos_total + 1 WHERE id = ? AND encuesta_id = ?")) {
                    ps.setLong(1, opcionId);
                    ps.setLong(2, encuestaId);
                    int n = ps.executeUpdate();
                    if (n == 0) {
                        c.rollback();
                        r.exito = false;
                        r.mensaje = "Opcion no valida";
                        return r;
                    }
                }

                c.commit();
                r.exito = true;
                r.mensaje = "Voto OK";
                r.recibo = sha256(codigo + System.nanoTime());
                return r;

            } catch (Exception ex) {
                c.rollback();
                throw new RuntimeException("Error emitiendo voto: " + ex.getMessage(), ex);
            } finally {
                c.setAutoCommit(true);
            }
        } catch (Exception e) {
            throw new RuntimeException(e.getMessage(), e);
        }
    }

    private String sha256(String s) {
        try {
            MessageDigest md = MessageDigest.getInstance("SHA-256");
            byte[] h = md.digest(s.getBytes(StandardCharsets.UTF_8));
            StringBuilder sb = new StringBuilder();
            for (byte b : h) {
                sb.append(String.format("%02x", b));
            }
            return sb.toString().substring(0, 16).toUpperCase();
        } catch (Exception e) {
            return Long.toHexString(System.nanoTime()).toUpperCase();
        }
    }
}
