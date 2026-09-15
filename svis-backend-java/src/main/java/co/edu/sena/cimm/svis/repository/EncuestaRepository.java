package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Encuesta;
import java.util.List;

public interface EncuestaRepository {

    List<Encuesta> listarActivas();

    List<Encuesta> listarTodas();

    long crearEncuesta(String titulo, String descripcion);

    Encuesta obtenerEncuesta(Long id);
}
