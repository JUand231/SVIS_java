package co.edu.cimm.svis.service;

import co.edu.sena.cimm.svis.config.AppContext;
import co.edu.sena.cimm.svis.model.Usuario;
import co.edu.sena.cimm.svis.repository.UsuarioRepository;

public class AuthService {

    private final UsuarioRepository usuarios = AppContext.get().getUsuarioRepository();

    public Usuario loginAdmin(String username, String contrasenia) {
        if (username == null || username.trim().isEmpty()) {
            throw new RuntimeException("Username requerido");
        }
        if (contrasenia == null || contrasenia.isEmpty()) {
            throw new RuntimeException("Contraseña requerida");
        }
        return usuarios.loginAdmin(username.trim(), contrasenia);
    }

    public Usuario loginVotante(String documento, String numeroFicha) {
        if (documento == null || documento.trim().isEmpty()) {
            throw new RuntimeException("Documento requerido");
        }
        if (numeroFicha == null || numeroFicha.isEmpty()) {
            throw new RuntimeException("Numero de ficha requerido");
        }
        return usuarios.loginVotante(documento.trim(), numeroFicha);
    }
}
