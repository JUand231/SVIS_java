package co.edu.sena.cimm.svis.servlet;

import co.edu.sena.cimm.svis.config.AppContext;
import co.edu.sena.cimm.svis.dto.ApiError;
import co.edu.sena.cimm.svis.dto.EncuestaRequest;
import co.edu.sena.cimm.svis.service.EncuestaService;
import co.edu.sena.cimm.svis.util.JsonUtil;
import java.io.BufferedReader;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

@WebServlet("/api/encuestas")
public class EncuestaServlet extends HttpServlet {

    private EncuestaService service;

    @Override
    public void init() {
        service = new EncuestaService(
                AppContext.get().getEncuestaRepository(),
                AppContext.get().getOpcionRepository());
    }

    // GET /api/encuestas -> activas | ?estado=todas -> todas | ?id=1 -> una
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");
        try {
            String idParam = req.getParameter("id");
            if (idParam != null && !idParam.trim().isEmpty()) {
                Long id = Long.parseLong(idParam.trim());
                resp.setStatus(HttpServletResponse.SC_OK); // 200
                resp.getWriter().write(JsonUtil.toJson(service.obtenerPorId(id)));
                return;
            }
            String estado = req.getParameter("estado");
            Object lista = "todas".equalsIgnoreCase(estado) ? service.listarTodas() : service.listarActivas();
            resp.setStatus(HttpServletResponse.SC_OK); // 200
            resp.getWriter().write(JsonUtil.toJson(lista));
        } catch (NumberFormatException e) {
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ID_INVALIDO", "id debe ser numero")));
        } catch (RuntimeException e) {
            resp.setStatus(HttpServletResponse.SC_NOT_FOUND); // 404
            resp.getWriter().write(JsonUtil.toJson(new ApiError("NO_ENCONTRADO", e.getMessage())));
        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR); // 500
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
    }

    // POST /api/encuestas {"titulo","descripcion","opciones":[...]} -> 201 {id}
    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");
        try {
            BufferedReader r = req.getReader();
            StringBuilder sb = new StringBuilder();
            String linea;
            while ((linea = r.readLine()) != null) {
                sb.append(linea);
            }
            EncuestaRequest in = JsonUtil.fromJson(sb.toString(), EncuestaRequest.class);
            if (in == null) {
                resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400
                resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", "Cuerpo requerido")));
                return;
            }
            long id = service.crearEncuesta(in.titulo, in.descripcion, in.opciones);
            Map<String, Object> out = new HashMap<>();
            out.put("id", id);
            resp.setStatus(HttpServletResponse.SC_CREATED); // 201
            resp.getWriter().write(JsonUtil.toJson(out));
        } catch (RuntimeException e) {
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400 titulo/opciones
            resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", e.getMessage())));
        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR); // 500
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
    }

    // PUT /api/encuestas?id=1 -> cerrar (irreversible)
    @Override
    protected void doPut(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");
        try {
            String idParam = req.getParameter("id");
            if (idParam == null || idParam.trim().isEmpty()) {
                resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400
                resp.getWriter().write(JsonUtil.toJson(new ApiError("FALTA_ID", "id requerido")));
                return;
            }
            Long id = Long.parseLong(idParam.trim());
            service.cerrar(id);
            Map<String, Object> out = new HashMap<>();
            out.put("id", id);
            out.put("estado", "CERRADA");
            resp.setStatus(HttpServletResponse.SC_OK); // 200
            resp.getWriter().write(JsonUtil.toJson(out));
        } catch (NumberFormatException e) {
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ID_INVALIDO", "id debe ser numero")));
        } catch (RuntimeException e) {
            resp.setStatus(HttpServletResponse.SC_NOT_FOUND); // 404
            resp.getWriter().write(JsonUtil.toJson(new ApiError("NO_ENCONTRADO", e.getMessage())));
        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR); // 500
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
    }
}
