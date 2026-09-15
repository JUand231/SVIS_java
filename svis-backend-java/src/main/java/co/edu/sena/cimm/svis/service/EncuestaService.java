package co.edu.sena.cimm.svis.service;

import co.edu.sena.cimm.svis.dto.ResultadoOpcion;
import co.edu.sena.cimm.svis.model.Encuesta;
import co.edu.sena.cimm.svis.model.Opcion;
import co.edu.sena.cimm.svis.repository.EncuestaRepository;
import co.edu.sena.cimm.svis.repository.OpcionRepository;
import java.util.ArrayList;
import java.util.List;

public class EncuestaService {

    private final EncuestaRepository encuestasRepository;
    private final OpcionRepository opcionesRepository;

    public EncuestaService(EncuestaRepository encuestasRepository, OpcionRepository opcionesRepository) {
        this.encuestasRepository = encuestasRepository;
        this.opcionesRepository = opcionesRepository;
    }

    public long crearEncuesta(String titulo, String descripcion, List<String> textosOpciones) {
        if (titulo == null || titulo.trim().isEmpty()) {
            throw new RuntimeException("Titulo requerido");
        }
        if (textosOpciones == null) {
            throw new RuntimeException("Minimo 2 opciones");
        }
        List<String> limpias = new ArrayList<>();
        List<String> jornadas = new ArrayList<>();
        for (String t : textosOpciones) {
            if (t != null && !t.trim().isEmpty()) {
                // Formato por línea: "Texto | Jornada" (la jornada es opcional).
                String[] partes = t.trim().split("\\|", 2);
                limpias.add(partes[0].trim());
                String j = partes.length > 1 ? partes[1].trim() : "";
                jornadas.add(j.isEmpty() ? null : j);
            }
        }
        if (limpias.size() < 2) {
            throw new RuntimeException("Minimo 2 opciones");
        }
        long nuevaId = encuestasRepository.crearEncuesta(titulo.trim(), descripcion == null ? "" : descripcion.trim());

        for (int i = 0; i < limpias.size(); i++) {
            opcionesRepository.crearOpcion(nuevaId, null, limpias.get(i), null, jornadas.get(i));
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
            r.jornada = o.getJornada();
            r.votos = o.getVotos_total();
            r.porcentaje = total == 0 ? 0 : (o.getVotos_total() * 100.0 / total);
            res.add(r);
        }
        return res;
    }
}
