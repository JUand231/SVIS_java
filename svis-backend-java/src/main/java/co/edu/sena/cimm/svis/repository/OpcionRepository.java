package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Opcion;
import java.util.List;

public interface OpcionRepository {

    List<Opcion> listarOpcionesPorEncuesta(Long idEncuesta);

    void crearOpcion(Long idEncuesta, String texto);

    int obtenerContadorVotos(Long idOpcion);

    void sumarVoto(Long idOpcion);
}
