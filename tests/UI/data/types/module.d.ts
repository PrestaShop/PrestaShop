type ModuleDataCreator = {
  tag?: string
  name?: string
  releaseZip?: string
}

type ModuleInfo = {
  moduleId: number
  technicalName: string
  version: string
  enabled: boolean
}

export {
  ModuleDataCreator,
  ModuleInfo,
};
