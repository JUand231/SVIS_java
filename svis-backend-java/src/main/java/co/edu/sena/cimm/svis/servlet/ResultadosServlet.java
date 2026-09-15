package co.edu.sena.cimm.svis.servlet;

import co.edu.sena.cimm.svis.config.AppContext;
import co.edu.sena.cimm.svis.dto.ApiError;
import co.edu.sena.cimm.svis.service.EncuestaService;
import co.edu.sena.cimm.svis.util.JsonUtil;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;

@WebServlet("/api/resultados")
public class ResultadosServlet extends HttpServlet {

    private EncuestaService service;

    @Override
    public void init() {
        service = new EncuestaService(
                AppContext.get().getEncuestaRepository(),
                AppContext.get().getOpcionRepository(),
                AppContext.get().getUsuarioRepository(),
                AppContext.get().getFichaRepository());
    }

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");

        // 1. Leer query param ?encuestaId=
        String idParam = req.getParameter("encuestaId");
        if (idParam == null || idParam.trim().isEmpty()) {
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400
            resp.getWriter().write(JsonUtil.toJson(new ApiError("FALTA_ID", "encuestaId requerido")));
            return;
        }

        try {
            // 2. Llamar al service (mira EncuestaService.java:55 resultados())
            Long id = Long.parseLong(idParam.trim());
            Object datos = service.resultados(id);

            // 3. OK
            resp.setStatus(HttpServletResponse.SC_OK); // 200
            resp.getWriter().write(JsonUtil.toJson(datos));

        } catch (NumberFormatException e) {
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ID_INVALIDO", "encuestaId debe ser numero")));
        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR); // 500
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
    }
}
