interface RendererType {
  toggleLoading: (toggle: boolean) => void;
  render: (data: Record<string, unknown>) => void;
}

export default RendererType;
