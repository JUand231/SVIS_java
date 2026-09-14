package co.edu.sena.cimm.svis.config;

import co.edu.sena.cimm.svis.repository.EncuestaRepository;
import co.edu.sena.cimm.svis.repository.FichaRepository;
import co.edu.sena.cimm.svis.repository.JdbcEncuestaRepository;
import co.edu.sena.cimm.svis.repository.JdbcFichaRepository;
import co.edu.sena.cimm.svis.repository.JdbcOpcionRepository;
import co.edu.sena.cimm.svis.repository.JdbcTokenOtpRepository;
import co.edu.sena.cimm.svis.repository.JdbcUsuarioRepository;
import co.edu.sena.cimm.svis.repository.OpcionRepository;
import co.edu.sena.cimm.svis.repository.TokenOtpRepository;
import co.edu.sena.cimm.svis.repository.UsuarioRepository;

public class AppContext {

    private static final AppContext INSTANCE = new AppContext();

    private final EncuestaRepository encuestaRepository;
    private final OpcionRepository opcionRepository;
    private final UsuarioRepository usuarioRepository;
    private final TokenOtpRepository tokenOtpRepository;
    private final FichaRepository fichaRepository;

    private AppContext() {
        this.encuestaRepository = new JdbcEncuestaRepository();
        this.opcionRepository = new JdbcOpcionRepository();
        this.usuarioRepository = new JdbcUsuarioRepository();
        this.tokenOtpRepository = new JdbcTokenOtpRepository();
        this.fichaRepository = new JdbcFichaRepository();
    }

    public static AppContext get() {
        return INSTANCE;
    }

    public EncuestaRepository getEncuestaRepository() { return encuestaRepository; }
    public OpcionRepository getOpcionRepository() { return opcionRepository; }
    public UsuarioRepository getUsuarioRepository() { return usuarioRepository; }
    public TokenOtpRepository getTokenOtpRepository() { return tokenOtpRepository; }
    public FichaRepository getFichaRepository() { return fichaRepository; }
}
