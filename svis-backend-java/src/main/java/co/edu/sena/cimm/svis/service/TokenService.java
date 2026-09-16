package co.edu.sena.cimm.svis.service;

import co.edu.sena.cimm.svis.config.AppContext;
import co.edu.sena.cimm.svis.repository.TokenOtpRepository;
import co.edu.sena.cimm.svis.repository.UsuarioRepository;
import java.time.LocalDateTime;
import java.util.List;
import java.util.Random;

public class TokenService {

    private final TokenOtpRepository tokens = AppContext.get().getTokenOtpRepository();
    private final UsuarioRepository usuarios = AppContext.get().getUsuarioRepository();
    private final Random rnd = new Random();

    public int generarParaEncuesta(Long encuestaId, Integer diasExpiracion) {
        if (encuestaId == null) {
            throw new RuntimeException("Encuesta requerida");
        }
        int dias = diasExpiracion == null ? 7 : diasExpiracion;
        LocalDateTime exp = LocalDateTime.now().plusDays(dias);

        List<Long> ids = usuarios.listarIdsPorRol("USUARIO");
        int creados = 0;
        for (Long uid : ids) {
            for (int intento = 0; intento < 10; intento++) {
                String codigo = String.format("%06d", rnd.nextInt(1000000));
                try {
                    tokens.generarYGuardar(encuestaId, uid, codigo, exp);
                    creados++;
                    break;
                } catch (RuntimeException e) {

                }
            }
        }
        return creados;
    }

    public java.util.List<co.edu.sena.cimm.svis.model.Token_OTP> listarPorEncuesta(Long encuestaId) {
        if (encuestaId == null) throw new RuntimeException("Encuesta requerida");
        return tokens.listarPorEncuesta(encuestaId);
    }
}
