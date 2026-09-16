package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Opcion;
import java.util.List;

public interface OpcionRepository {

    List<Opcion> listarOpcionesPorEncuesta(Long idEncuesta);

    void crearOpcion(Long idEncuesta, Long candidatoId, String texto, String fotoUrl, String propuestas);

    int obtenerContadorVotos(Long idOpcion);

    void sumarVoto(Long idOpcion);
}
