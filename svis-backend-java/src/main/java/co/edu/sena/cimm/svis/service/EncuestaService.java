package co.edu.sena.cimm.svis.service;

import co.edu.sena.cimm.svis.dto.ResultadoOpcion;
import co.edu.sena.cimm.svis.model.Encuesta;
import co.edu.sena.cimm.svis.model.Ficha;
import co.edu.sena.cimm.svis.model.Opcion;
import co.edu.sena.cimm.svis.model.Usuario;
import co.edu.sena.cimm.svis.repository.EncuestaRepository;
import co.edu.sena.cimm.svis.repository.FichaRepository;
import co.edu.sena.cimm.svis.repository.OpcionRepository;
import co.edu.sena.cimm.svis.repository.UsuarioRepository;
import java.util.ArrayList;
import java.util.List;

public class EncuestaService {

    private final EncuestaRepository encuestasRepository;
    private final OpcionRepository opcionesRepository;
    private final UsuarioRepository usuariosRepository;
    private final FichaRepository fichasRepository;

    public EncuestaService(EncuestaRepository encuestasRepository, OpcionRepository opcionesRepository,
            UsuarioRepository usuariosRepository, FichaRepository fichasRepository) {
        this.encuestasRepository = encuestasRepository;
        this.opcionesRepository = opcionesRepository;
        this.usuariosRepository = usuariosRepository;
        this.fichasRepository = fichasRepository;
    }

    public long crearEncuesta(String titulo, String descripcion, List<String> textosOpciones) {
        if (titulo == null || titulo.trim().isEmpty()) {
            throw new RuntimeException("Titulo requerido");
        }
        if (textosOpciones == null) {
            throw new RuntimeException("Minimo 2 opciones");
        }
        // Formato por línea: "Nombre | documentoCandidato | fotoUrl"
        // (documento y foto opcionales; el documento enlaza la ficha real).
        List<String> textos = new ArrayList<>();
        List<Long> candidatoIds = new ArrayList<>();
        List<String> fotos = new ArrayList<>();
        for (String t : textosOpciones) {
            if (t == null || t.trim().isEmpty()) {
                continue;
            }
            String[] partes = t.trim().split("\\|", 3);
            String texto = partes[0].trim();
            if (texto.isEmpty()) {
                continue;
            }
            String doc = partes.length > 1 ? partes[1].trim() : "";
            String foto = partes.length > 2 ? partes[2].trim() : "";
            Long candId = null;
            if (!doc.isEmpty()) {
                Usuario u = usuariosRepository.obtenerPorDocumento(doc);
                if (u == null) {
                    throw new RuntimeException("No existe usuario con documento: " + doc);
                }
                candId = u.getId();
            }
            textos.add(texto);
            candidatoIds.add(candId);
            fotos.add(foto.isEmpty() ? null : foto);
        }
        if (textos.size() < 2) {
            throw new RuntimeException("Minimo 2 opciones");
        }
        long nuevaId = encuestasRepository.crearEncuesta(titulo.trim(), descripcion == null ? "" : descripcion.trim());

        for (int i = 0; i < textos.size(); i++) {
            opcionesRepository.crearOpcion(nuevaId, candidatoIds.get(i), textos.get(i), fotos.get(i));
        }
        return nuevaId;
    }

    public List<Encuesta> listarActivas() {
        return encuestasRepository.listarActivas();
    }

    public List<Encuesta> listarTodas() {
        return encuestasRepository.listarTodas();
    }

    public Encuesta obtenerPorId(Long id) {
        if (id == null) {
            throw new RuntimeException("Id requerido");
        }
        return encuestasRepository.obtenerEncuesta(id);
    }

    public void cerrar(Long id) {
        if (id == null) {
            throw new RuntimeException("Id requerido");
        }
        encuestasRepository.cerrarEncuesta(id);
    }

    public List<ResultadoOpcion> resultados(Long encuestaId) {
        if (encuestaId == null) {
            throw new RuntimeException("Encuesta requerida");
        }
        List<Opcion> ops = opcionesRepository.listarOpcionesPorEncuesta(encuestaId);
        int total = 0;
        for (Opcion o : ops) {
            total += o.getVotos_total();
        }
        List<ResultadoOpcion> res = new ArrayList<>();
        for (Opcion o : ops) {
            ResultadoOpcion r = new ResultadoOpcion();
            r.opcionId = o.getId();
            r.texto = o.getTexto();
            r.fotoUrl = o.getFoto_url();
            // Jornada y programa reales: opcion -> candidato -> ficha.
            r.jornada = null;
            r.programa = null;
            if (o.getCandidato_id() != null) {
                try {
                    Usuario u = usuariosRepository.obtenerPorId(o.getCandidato_id());
                    if (u != null && u.getFicha_id() != null) {
                        Ficha f = fichasRepository.obtenerPorId(u.getFicha_id());
                        r.jornada = f.getJornada();
                        r.programa = f.getPrograma();
                    }
                } catch (RuntimeException e) {
                    // Sin ficha enlazada: se deja null y el front lo omite.
                }
            }
            r.votos = o.getVotos_total();
            r.porcentaje = total == 0 ? 0 : (o.getVotos_total() * 100.0 / total);
            res.add(r);
        }
        return res;
    }
}
